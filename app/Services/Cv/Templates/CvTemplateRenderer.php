<?php

namespace App\Services\Cv\Templates;

use App\DTOs\Cv\CvDocumentData;
use App\Models\User;
use App\Services\BaseService;
use App\Services\Cv\CvPreviewTransformer;
use App\Support\Cv\CvSectionRenderPipeline;
use App\Support\Cv\CvTemplateLayout;
use App\Support\Cv\CvTemplateLayoutResolver;

class CvTemplateRenderer extends BaseService
{
    public function __construct(
        protected TemplateRegistry $registry,
        protected CvPreviewTransformer $previewTransformer,
        protected CvJsonRenderer $jsonRenderer,
    ) {}

    /**
     * @param  array<string, mixed>  $options
     */
    public function render(
        CvDocumentData $document,
        ?string $templateKey = null,
        array $options = [],
    ): string {
        $templateKey = $this->resolveTemplateKey($document, $templateKey);
        $definition = $this->registry->resolve($templateKey) ?? $this->defaultDefinition($templateKey);
        $templateId = $document->template->id ?? $definition['id'] ?? null;

        if ($templateId === null && ! empty($options['template_id'])) {
            $templateId = (int) $options['template_id'];
        }

        $layoutConfig = CvTemplateLayoutResolver::resolveForDocument($document, $templateId);
        $preview = $this->previewTransformer->transform($document);
        $mode = $options['mode'] ?? 'preview';
        $modeConfig = config("cv-templates.modes.{$mode}", []);
        $settings = CvTemplateLayoutResolver::mergeSettings(
            $layoutConfig['settings'],
            $document->template->settings ?? [],
        );

        if (! empty($options['settings'])) {
            $settings = CvTemplateLayoutResolver::mergeSettings($settings, $options['settings']);
        }

        $watermark = (bool) ($options['watermark'] ?? false);
        $renderSections = CvSectionRenderPipeline::build($preview, $layoutConfig);
        $sectionLayout = CvTemplateLayoutResolver::splitSections($renderSections, $layoutConfig);

        return view(config('cv-templates.renderer.wrapper'), [
            'preview' => $preview,
            'renderSections' => $renderSections,
            'definition' => $definition,
            'layout' => $layoutConfig['layout'],
            'layoutConfig' => $layoutConfig,
            'sectionLayout' => $sectionLayout,
            'settings' => $settings,
            'mode' => $mode,
            'modeConfig' => $modeConfig,
            'watermark' => $watermark,
            'watermarkPrimaryText' => config('cv-templates.watermark.primary_text', 'FREE VERSION'),
            'watermarkSecondaryText' => config('cv-templates.watermark.secondary_text', 'Generated with Free Plan'),
            'jsonRenderer' => $this->jsonRenderer,
            'fontUrl' => CvTemplateLayoutResolver::pdfFontUrl($settings['font'] ?? 'figtree', $mode),
            'fontFamily' => CvTemplateLayoutResolver::pdfFontFamily($settings['font'] ?? 'figtree', $mode),
            'pageStyle' => in_array($mode, ['preview', 'embedded', 'export', 'pdf'], true)
                ? CvTemplateLayoutResolver::previewInlineStyle($settings)
                : CvTemplateLayoutResolver::inlineStyle($settings),
        ])->render();
    }

    public function renderFromPreview(
        array $preview,
        string $templateKey,
        array $options = [],
    ): string {
        $definition = $this->registry->resolve($templateKey) ?? $this->defaultDefinition($templateKey);
        $templateId = $preview['template']['id'] ?? $definition['id'] ?? $options['template_id'] ?? null;
        $document = CvDocumentData::fromArray($preview);
        $layoutConfig = CvTemplateLayoutResolver::resolveForDocument($document, $templateId);
        $mode = $options['mode'] ?? 'preview';
        $modeConfig = config("cv-templates.modes.{$mode}", []);
        $settings = CvTemplateLayoutResolver::mergeSettings(
            $layoutConfig['settings'],
            $preview['template']['settings'] ?? [],
        );

        if (! empty($options['settings'])) {
            $settings = CvTemplateLayoutResolver::mergeSettings($settings, $options['settings']);
        }

        $watermark = (bool) ($options['watermark'] ?? false);
        $renderSections = CvSectionRenderPipeline::build($preview, $layoutConfig);
        $sectionLayout = CvTemplateLayoutResolver::splitSections($renderSections, $layoutConfig);

        return view(config('cv-templates.renderer.wrapper'), [
            'preview' => $preview,
            'renderSections' => $renderSections,
            'definition' => $definition,
            'layout' => $layoutConfig['layout'],
            'layoutConfig' => $layoutConfig,
            'sectionLayout' => $sectionLayout,
            'settings' => $settings,
            'mode' => $mode,
            'modeConfig' => $modeConfig,
            'watermark' => $watermark,
            'watermarkPrimaryText' => config('cv-templates.watermark.primary_text', 'FREE VERSION'),
            'watermarkSecondaryText' => config('cv-templates.watermark.secondary_text', 'Generated with Free Plan'),
            'jsonRenderer' => $this->jsonRenderer,
            'fontUrl' => CvTemplateLayoutResolver::pdfFontUrl($settings['font'] ?? 'figtree', $mode),
            'fontFamily' => CvTemplateLayoutResolver::pdfFontFamily($settings['font'] ?? 'figtree', $mode),
            'pageStyle' => in_array($mode, ['preview', 'embedded', 'export', 'pdf'], true)
                ? CvTemplateLayoutResolver::previewInlineStyle($settings)
                : CvTemplateLayoutResolver::inlineStyle($settings),
        ])->render();
    }

    protected function resolveTemplateKey(CvDocumentData $document, ?string $override): string
    {
        $key = $override
            ?? $document->template->slug
            ?? $document->template->key
            ?? null;

        if ($key && $this->registry->exists($key)) {
            return $key;
        }

        $first = $this->registry->keys()[0] ?? null;

        return $first ?? 'default';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultDefinition(string $key): array
    {
        $layout = CvTemplateLayout::normalize([
            'layout' => config('cv-templates.renderer.fallback', 'classic'),
        ]);

        return [
            'key' => $key,
            'slug' => $key,
            'id' => null,
            'name' => ucfirst($key),
            'description' => '',
            'layout' => $layout['layout'],
            'layout_json' => $layout,
            'settings' => $layout['settings'],
            'section_order' => $layout['section_order'],
            'enabled_sections' => $layout['enabled_sections'],
            'thumbnail' => null,
        ];
    }

    public function shouldWatermark(?User $user): bool
    {
        if (! $user) {
            return true;
        }

        return app(\App\Services\Subscription\SubscriptionService::class)->shouldApplyWatermark($user);
    }
}
