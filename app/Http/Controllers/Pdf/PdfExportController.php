<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Concerns\RespondsToPdfExportFailures;
use App\Http\Requests\Cv\RenderCvPreviewRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pdf\QueueCvPdfExportRequest;
use App\Models\CoverLetter;
use App\Models\Cv;
use App\Models\PdfExport;
use App\Services\Pdf\CoverLetterPdfGenerator;
use App\Services\Pdf\CvPdfExportService;
use App\Services\Pdf\CvPdfGenerator;
use App\Services\Pdf\PdfGenerationService;
use App\Services\Pdf\PdfExportService;
use App\Support\Pdf\PdfOutputGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            $filename = $this->cvPdfExportService->filename($cv);

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

    public function downloadCoverLetter(CoverLetter $coverLetter): Response
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

            $filename = Str::slug($coverLetter->title ?: 'cover-letter').'.pdf';

            return PdfOutputGuard::downloadResponse($pdf, $filename);
        } catch (Throwable $exception) {
            return $this->pdfExportErrorResponse($exception);
        }
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
