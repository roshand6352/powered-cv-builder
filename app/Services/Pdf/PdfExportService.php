<?php

namespace App\Services\Pdf;

use App\Enums\PdfExportStatus;
use App\Enums\PdfExportType;
use App\Jobs\GeneratePdfExportJob;
use App\Models\CoverLetter;
use App\Models\Cv;
use App\Models\PdfExport;
use App\Models\User;
use App\Services\BaseService;
use App\Support\Pdf\PdfOutputGuard;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class PdfExportService extends BaseService
{
    public function __construct(
        protected CvPdfGenerator $cvPdfGenerator,
        protected CoverLetterPdfGenerator $coverLetterPdfGenerator,
        protected PdfGenerationService $pdfGenerationService,
        protected CvPdfExportService $cvPdfExportService,
    ) {}

    public function queueCvExport(Cv $cv, User $user, ?string $templateKey = null): PdfExport
    {
        $export = $this->createExport(
            user: $user,
            exportable: $cv,
            type: PdfExportType::Cv,
            filename: $this->cvPdfExportService->filename($cv),
            options: array_filter(['template' => $templateKey]),
        );

        $this->dispatch($export);

        return $export;
    }

    public function queueCoverLetterExport(CoverLetter $coverLetter, User $user): PdfExport
    {
        $export = $this->createExport(
            user: $user,
            exportable: $coverLetter,
            type: PdfExportType::CoverLetter,
            filename: $this->buildFilename($coverLetter->slug, 'cover-letter'),
        );

        $this->dispatch($export);

        return $export;
    }

    public function process(PdfExport $export): void
    {
        $export->update(['status' => PdfExportStatus::Processing]);

        $export->loadMissing(['exportable', 'user.plan']);

        if ($export->user?->id) {
            $export->setRelation(
                'user',
                User::query()->with('plan')->find($export->user->id) ?? $export->user,
            );
        }

        if (! $export->exportable) {
            throw new \RuntimeException('Export source no longer exists.');
        }

        $templateKey = $export->options['template'] ?? null;

        if ($export->type === PdfExportType::Cv) {
            /** @var Cv $cv */
            $cv = $export->exportable;
            $userId = (int) $export->user->id;
            $applyWatermark = $this->cvPdfGenerator->shouldWatermarkForUserId($userId);
            $html = $this->cvPdfGenerator->renderHtml($cv, $templateKey, $export->user, $applyWatermark);
            $html = $this->cvPdfGenerator->prepareLivePreviewHtml($html, $applyWatermark);
            $options = $this->cvPdfGenerator->buildPreviewMatchedOptions(
                $cv,
                $templateKey,
                $export->user,
                $applyWatermark,
                fromLivePreview: true,
            );
        } else {
            /** @var CoverLetter $coverLetter */
            $coverLetter = $export->exportable;
            $userId = (int) $export->user->id;
            $applyWatermark = $this->coverLetterPdfGenerator->shouldWatermarkForUserId($userId);
            $html = $this->coverLetterPdfGenerator->renderHtml($coverLetter, $export->user, $applyWatermark);
            $html = $this->cvPdfGenerator->prepareLivePreviewHtml($html, $applyWatermark);
            $options = $this->coverLetterPdfGenerator->buildOptions($coverLetter, $export->user, $applyWatermark);
        }

        $pdf = $this->pdfGenerationService->generate($html, $options);
        PdfOutputGuard::assertValidPdf($pdf);
        $path = $this->storePdf($export, $pdf);

        $export->update([
            'status' => PdfExportStatus::Completed,
            'path' => $path,
            'file_size' => strlen($pdf),
            'completed_at' => now(),
            'error_message' => null,
        ]);
    }

    public function markFailed(PdfExport $export, Throwable $exception): void
    {
        $export->refresh();

        if ($export->status === PdfExportStatus::Completed) {
            return;
        }

        $export->update([
            'status' => PdfExportStatus::Failed,
            'error_message' => Str::limit($exception->getMessage(), 1000),
        ]);
    }

    /**
     * Save a directly downloaded PDF to the user's Document Library so it can be
     * downloaded again later. Keeps one entry per document, holding its latest PDF.
     */
    public function recordDownload(User $user, Cv|CoverLetter $exportable, string $filename, string $binary): PdfExport
    {
        PdfOutputGuard::assertValidPdf($binary);

        $export = PdfExport::query()
            ->where('user_id', $user->id)
            ->where('exportable_type', $exportable::class)
            ->where('exportable_id', $exportable->id)
            ->where('status', PdfExportStatus::Completed)
            ->latest('id')
            ->first();

        $previousDisk = $export?->disk;
        $previousPath = $export?->path;

        $export ??= $this->createExport(
            user: $user,
            exportable: $exportable,
            type: $exportable instanceof Cv ? PdfExportType::Cv : PdfExportType::CoverLetter,
            filename: $filename,
        );

        $export->filename = $filename;
        $path = $this->storePdf($export, $binary);

        $export->forceFill([
            'status' => PdfExportStatus::Completed,
            'path' => $path,
            'file_size' => strlen($binary),
            'completed_at' => now(),
            'error_message' => null,
        ])->save();

        if ($previousPath && ($previousPath !== $path || $previousDisk !== $export->disk)) {
            Storage::disk($previousDisk)->delete($previousPath);
        }

        return $export;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, PdfExport>
     */
    public function historyForUser(User $user, int $limit = 25)
    {
        return PdfExport::query()
            ->where('user_id', $user->id)
            ->with('exportable')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    protected function createExport(
        User $user,
        Cv|CoverLetter $exportable,
        PdfExportType $type,
        string $filename,
        array $options = [],
    ): PdfExport {
        return PdfExport::query()->create([
            'user_id' => $user->id,
            'exportable_type' => $exportable::class,
            'exportable_id' => $exportable->id,
            'type' => $type,
            'filename' => $filename,
            'disk' => config('pdf.disk', 'local'),
            'status' => PdfExportStatus::Pending,
            'options' => $options ?: null,
        ]);
    }

    protected function dispatch(PdfExport $export): void
    {
        GeneratePdfExportJob::dispatch($export)
            ->onQueue(config('pdf.queue', 'default'));
    }

    protected function storePdf(PdfExport $export, string $binary): string
    {
        $directory = trim(config('pdf.directory', 'pdf-exports'), '/');
        $path = "{$directory}/{$export->user_id}/{$export->id}-{$export->filename}";

        Storage::disk($export->disk)->put($path, $binary);

        return $path;
    }

    protected function buildFilename(string $slug, string $prefix): string
    {
        return Str::slug($slug ?: $prefix).'.pdf';
    }
}
