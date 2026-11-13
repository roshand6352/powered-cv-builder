<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Concerns\RespondsToPdfExportFailures;
use App\Http\Requests\Cv\RenderCvPreviewRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pdf\QueueCvPdfExportRequest;
use App\Models\CoverLetter;
use App\Models\Cv;
use App\Models\PdfExport;
use App\Models\User;
use App\Services\Pdf\CoverLetterPdfGenerator;
use App\Services\Pdf\CvPdfExportService;
use App\Services\Pdf\CvPdfGenerator;
use App\Services\Pdf\PdfGenerationService;
use App\Services\Pdf\PdfExportService;
use App\Support\Pdf\PdfOutputGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class PdfExportController extends Controller
{
    use RespondsToPdfExportFailures;

    public function __construct(
        protected PdfExportService $pdfExportService,
        protected CvPdfExportService $cvPdfExportService,
        protected CvPdfGenerator $cvPdfGenerator,
        protected CoverLetterPdfGenerator $coverLetterPdfGenerator,
        protected PdfGenerationService $pdfGenerationService,
    ) {}

    public function downloadCv(RenderCvPreviewRequest $request, Cv $cv): Response
    {
        $this->authorize('view', $cv);

        set_time_limit(max(180, (int) config('browsershot.timeout', 120) + 30));

        $validated = $request->validated();
        $user = $request->user();

        try {
            $pdf = $this->cvPdfExportService->generate($cv, $user, $validated);
            $filename = $this->cvPdfExportService->filename($cv, $validated['json_data'] ?? null);

            $this->saveToDocumentLibrary($user, $cv, $filename, $pdf);

            return PdfOutputGuard::downloadResponse($pdf, $filename);
        } catch (Throwable $exception) {
            return $this->pdfExportErrorResponse($exception);
        }
    }

    public function storeCv(QueueCvPdfExportRequest $request, Cv $cv): JsonResponse
    {
        $this->authorize('view', $cv);

        $validated = $request->validated();

        try {
            $export = $this->pdfExportService->queueCvExport(
                $cv,
                $request->user(),
                $validated['template'] ?? null,
            );

            return response()->json([
                'message' => 'PDF export queued.',
                'export' => $this->exportPayload($export),
            ], 202);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to queue PDF export.',
                'error' => config('app.debug') ? $exception->getMessage() : null,
            ], 500);
        }
    }

    public function storeCoverLetter(Request $request, CoverLetter $coverLetter): JsonResponse
    {
        $this->authorize('view', $coverLetter);

        try {
            $export = $this->pdfExportService->queueCoverLetterExport(
                $coverLetter,
                $request->user(),
            );

            return response()->json([
                'message' => 'PDF export queued.',
                'export' => $this->exportPayload($export),
            ], 202);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to queue PDF export.',
                'error' => config('app.debug') ? $exception->getMessage() : null,
            ], 500);
        }
    }

    public function downloadCoverLetter(Request $request, CoverLetter $coverLetter): Response
    {
        $this->authorize('view', $coverLetter);

        set_time_limit(max(180, (int) config('browsershot.timeout', 120) + 30));

        $coverLetter->loadMissing(['cv', 'template', 'user.plan']);
        $user = $coverLetter->user;
        $applyWatermark = $this->coverLetterPdfGenerator->shouldWatermarkForUserId((int) $coverLetter->user_id);

        try {
            $html = $this->coverLetterPdfGenerator->renderHtml($coverLetter, $user, $applyWatermark);
            $html = $this->cvPdfGenerator->prepareLivePreviewHtml($html, $applyWatermark);

            $pdf = $this->pdfGenerationService->generate(
                $html,
                $this->coverLetterPdfGenerator->buildOptions($coverLetter, $user, $applyWatermark),
            );

            $filename = (Str::slug($coverLetter->title ?: 'cover-letter') ?: 'cover-letter').'.pdf';

            $this->saveToDocumentLibrary($request->user(), $coverLetter, $filename, $pdf);

            return PdfOutputGuard::downloadResponse($pdf, $filename);
        } catch (Throwable $exception) {
            return $this->coverLetterPdfFailureResponse($request, $coverLetter, $exception);
        }
    }

    /**
     * A library write failure must never block the download the user asked for.
     */
    protected function saveToDocumentLibrary(User $user, Cv|CoverLetter $document, string $filename, string $pdf): void
    {
        try {
            $this->pdfExportService->recordDownload($user, $document, $filename, $pdf);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    /**
     * Log the technical failure server side and return a friendly, non-technical
     * response. AJAX callers get JSON they can show with a Retry button; plain form
     * posts are redirected back to the page they came from with a flash message.
     */
    protected function coverLetterPdfFailureResponse(Request $request, CoverLetter $coverLetter, Throwable $exception): Response
    {
        $reference = Str::upper(Str::random(10));

        Log::error('Cover letter PDF generation failed.', [
            'reference' => $reference,
            'cover_letter_id' => $coverLetter->id,
            'user_id' => $request->user()?->id,
            'pdf_engine' => config('pdf.hosts'),
            'exception' => $exception::class,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile().':'.$exception->getLine(),
            'trace' => Str::limit($exception->getTraceAsString(), 4000),
        ]);

        $message = "We couldn't generate your PDF right now. Your cover letter is safe - please try again.";

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'reference' => $reference,
                'retryable' => true,
            ], 500);
        }

        return redirect()
            ->back(fallback: route('dashboard.cover-letters'))
            ->with('cover_letter_pdf_error', [
                'message' => $message,
                'reference' => $reference,
                'slug' => $coverLetter->slug,
            ]);
    }

    public function show(PdfExport $pdfExport): JsonResponse
    {
        $this->authorize('view', $pdfExport);

        return response()->json([
            'export' => $this->exportPayload($pdfExport),
        ]);
    }

    public function download(PdfExport $pdfExport): StreamedResponse
    {
        $this->authorize('download', $pdfExport);

        if (! $pdfExport->isDownloadable()) {
            abort(404, 'PDF export is not ready for download.');
        }

        if (! Storage::disk($pdfExport->disk)->exists($pdfExport->path)) {
            abort(404, 'PDF file not found.');
        }

        return Storage::disk($pdfExport->disk)->download(
            $pdfExport->path,
            $pdfExport->filename,
            ['Content-Type' => 'application/pdf'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function exportPayload(PdfExport $export): array
    {
        return [
            'id' => $export->id,
            'type' => $export->type->value,
            'type_label' => $export->type->label(),
            'filename' => $export->filename,
            'status' => $export->status->value,
            'status_label' => $export->status->label(),
            'error_message' => $export->error_message,
            'file_size' => $export->file_size,
            'completed_at' => $export->completed_at?->toIso8601String(),
            'created_at' => $export->created_at?->toIso8601String(),
            'download_url' => $export->isDownloadable()
                ? route('pdf-exports.download', $export)
                : null,
        ];
    }
}
