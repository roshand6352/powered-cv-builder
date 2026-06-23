<?php

namespace App\Services\Pdf;

use App\DTOs\Pdf\PdfGenerationOptions;
use App\Models\Cv;
use App\Models\User;
use App\Services\BaseService;
use App\Services\Cv\CvDocumentNormalizer;
use App\Services\Cv\Templates\CvTemplateRenderer;
use App\Services\Cv\Templates\TemplateRegistry;

class CvPdfGenerator extends BaseService
{
    public function __construct(
        protected CvTemplateRenderer $templateRenderer,
        protected CvDocumentNormalizer $normalizer,
        protected TemplateRegistry $templateRegistry,
        protected PdfLayoutService $layoutService,
        protected PdfWatermarkService $watermarkService,
        protected CvPreviewHtmlPdfPreparer $previewHtmlPreparer,
    ) {}

    public function renderHtml(Cv $cv, ?string $templateKey = null, ?User $user = null, ?bool $applyWatermark = null): string
    {
        return $this->renderHtmlFromData(
            $cv->cv_data ?? $cv->json_data ?? [],
            $templateKey,
            $user ?? $cv->user,
            $cv->template_id,
            $applyWatermark,
        );
    }

    public function shouldWatermarkForUserId(int $userId): bool
    {
        return $this->watermarkService->shouldApplyForUserId($userId);
    }

    /**
     * Use the builder live-preview HTML as-is for PDF (WYSIWYG export).
     */
    public function prepareLivePreviewHtml(string $html, bool $watermark = false): string
    {
        return $this->previewHtmlPreparer->prepare($html, $watermark);
    }

    /**
     * @param  array<string, mixed>  $jsonData
     */
    public function renderHtmlFromData(
        array $jsonData,
        ?string $templateKey = null,
        ?User $user = null,
        ?int $templateId = null,
        ?bool $applyWatermark = null,
    ): string {
        $document = $this->normalizer->normalize($jsonData);
        $templateKey = $templateKey
            ?? $document->template->key
            ?? $document->template->slug
            ?? config('cv-templates.renderer.fallback');

        $definition = $templateKey ? $this->templateRegistry->resolve($templateKey) : null;

        if ($definition) {
            $document->template->key = $templateKey;
            $document->template->slug = $templateKey;
            $document->template->id = $definition['id'] ?? $document->template->id ?? $templateId;
        }

        if ($applyWatermark === null) {
            $applyWatermark = $user?->id
                ? $this->watermarkService->shouldApplyForUserId($user->id)
                : false;
        }

        if ($definition && empty($document->template->id)) {
            $document->template->id = $definition['id'] ?? $templateId;
        }

        return $this->templateRenderer->render($document, $templateKey, [
            'mode' => 'export',
            'user' => $user,
            'watermark' => $applyWatermark,
            'template_id' => $document->template->id ?? $definition['id'] ?? $templateId,
        ]);
    }

    public function buildOptions(Cv $cv, ?string $templateKey = null): PdfGenerationOptions
    {
        return $this->buildPreviewMatchedOptions($cv, $templateKey);
    }

    public function buildPreviewMatchedOptions(
        Cv $cv,
        ?string $templateKey = null,
        ?User $user = null,
        ?bool $applyWatermark = null,
        bool $fromLivePreview = false,
    ): PdfGenerationOptions {
        $meta = [
            'title' => $cv->title,
            'type' => 'cv',
            'template' => $templateKey ?? data_get($cv->cv_data ?? $cv->json_data, 'template.key'),
        ];

        if ($applyWatermark === null) {
            $applyWatermark = $user?->id
                ? $this->watermarkService->shouldApplyForUserId($user->id)
                : false;
        }

        return new PdfGenerationOptions(
            watermark: $applyWatermark,
            headerHtml: null,
            footerHtml: null,
            title: $cv->title,
            meta: $meta,
            margins: ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0],
            emulatePrint: ! $fromLivePreview,
            preserveExternalResources: false,
            fromLivePreview: $fromLivePreview,
            preferBrowsershot: $fromLivePreview,
        );
    }

    public function buildDocumentOptions(Cv $cv, ?string $templateKey = null): PdfGenerationOptions
    {
        $meta = [
            'title' => $cv->title,
            'type' => 'cv',
            'template' => $templateKey ?? data_get($cv->cv_data ?? $cv->json_data, 'template.key'),
        ];

        return new PdfGenerationOptions(
            watermark: $this->watermarkService->shouldApply($cv->user),
            headerHtml: config('pdf.header.enabled', true)
                ? $this->layoutService->headerHtml($meta)
                : null,
            footerHtml: config('pdf.footer.enabled', true)
                ? $this->layoutService->footerHtml($meta)
                : null,
            title: $cv->title,
            meta: $meta,
        );
    }
}
