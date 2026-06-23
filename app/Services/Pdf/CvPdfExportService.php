<?php

namespace App\Services\Pdf;

use App\Models\Cv;
use App\Models\User;
use App\Services\BaseService;
use App\Services\Cv\CvDocumentNormalizer;
use App\Services\Cv\CvTemplateApplicationService;
use Illuminate\Support\Str;

class CvPdfExportService extends BaseService
{
    public function __construct(
        protected CvPdfGenerator $cvPdfGenerator,
        protected PdfGenerationService $pdfGenerationService,
        protected CvDocumentNormalizer $normalizer,
        protected CvTemplateApplicationService $templateApplicationService,
    ) {}

    /**
     * Generate a CV PDF using the same WYSIWYG pipeline as the builder download.
     *
     * @param  array<string, mixed>  $validated  Expects json_data; optional template and preview_html
     */
    public function generate(Cv $cv, User $user, array $validated): string
    {
        $document = $this->normalizer->normalize($validated['json_data']);
        $templateKey = $validated['template']
            ?? $document->template->key
            ?? $document->template->slug;

        $user->loadMissing('plan');
        $applyWatermark = $this->cvPdfGenerator->shouldWatermarkForUserId((int) $user->id);

        $previewHtml = trim((string) ($validated['preview_html'] ?? ''));

        $jsonData = $validated['json_data'];

        if ($previewHtml === '' && is_string($templateKey) && $templateKey !== '') {
            ['document' => $document] = $this->templateApplicationService->applyToDocument($document, $templateKey);
            $jsonData = $document->toArray();
        }

        $html = $previewHtml !== ''
            ? $previewHtml
            : $this->cvPdfGenerator->renderHtmlFromData(
                $jsonData,
                $templateKey,
                $user,
                $document->template->id ?? $cv->template_id,
                $applyWatermark,
            );

        $html = $this->cvPdfGenerator->prepareLivePreviewHtml($html, $applyWatermark);

        return $this->pdfGenerationService->generate(
            $html,
            $this->cvPdfGenerator->buildPreviewMatchedOptions(
                $cv,
                $templateKey,
                $user,
                $applyWatermark,
                fromLivePreview: $previewHtml !== '',
            ),
        );
    }

    public function filename(Cv $cv): string
    {
        return Str::slug($cv->title ?: 'cv').'.pdf';
    }
}
