<?php

namespace App\DTOs\Cv;

use App\Enums\CvStatus;
use App\Models\Cv;
use App\Services\Cv\CvPreviewTransformer;
use App\Services\Cv\Templates\TemplateRegistry;
use App\Support\Cv\CvDocumentMigrator;
use App\Support\Cv\CvTemplateLayout;
use Illuminate\Contracts\Support\Arrayable;

class CvBuilderData implements Arrayable
{
    /**
     * @param  array<int, array<string, mixed>>  $templateCatalog
     * @param  array<string, mixed>  $preview
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public CvStatus $status,
        public CvDocumentData $document,
        public array $preview,
        public array $templateCatalog = [],
        public ?int $templateId = null,
        public bool $isUpdateMode = true,
    ) {}

    /**
     * @param  array<int, array<string, mixed>>|null  $templateCatalog
     */
    public static function fromModel(Cv $cv, ?CvPreviewTransformer $previewTransformer = null, ?array $templateCatalog = null): self
    {
        $cv->loadMissing('cvTemplate');

        $document = CvDocumentData::fromArray($cv->cv_data ?? $cv->json_data ?? []);
        static::applyCvTemplate($document, $cv);

        $previewTransformer ??= app(CvPreviewTransformer::class);
        $templateCatalog ??= app(TemplateRegistry::class)->all()->all();

        return new self(
            id: $cv->id,
            title: $cv->title,
            slug: (string) $cv->id,
            status: CvStatus::resolve($cv->status),
            document: $document,
            preview: $previewTransformer->transform($document),
            templateCatalog: $templateCatalog,
            templateId: $cv->template_id,
            isUpdateMode: true,
        );
    }

    protected static function applyCvTemplate(CvDocumentData $document, Cv $cv): void
    {
        if (! $cv->cvTemplate) {
            return;
        }

        $layout = CvTemplateLayout::normalize($cv->cvTemplate->layout_json);

        $document->template->id = $cv->cvTemplate->id;
        $document->template->slug = $cv->cvTemplate->slug;
        $document->template->key = $cv->cvTemplate->slug;
        $document->template->settings = $layout['settings'] ?? [];
        $document->meta->sectionOrder = CvDocumentMigrator::migrateSectionKeys(
            $layout['section_order'] ?? $document->meta->sectionOrder,
        );

        $templateEnabled = CvDocumentMigrator::enabledSectionsFromTemplate(
            $layout['enabled_sections'] ?? [],
        );
        $savedEnabled = CvDocumentMigrator::normalizeEnabledSectionKeys(
            $document->meta->enabledSections,
        );

        if ($savedEnabled === []) {
            $document->meta->enabledSections = $templateEnabled;
        } else {
            $allowed = array_flip($templateEnabled);
            $preserved = array_values(array_filter(
                $savedEnabled,
                fn (string $key): bool => isset($allowed[$key]),
            ));

            $document->meta->enabledSections = $preserved !== []
                ? $preserved
                : $templateEnabled;
        }
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status->toApiValue(),
            'template_id' => $this->templateId,
            'active_template' => $this->document->template->slug
                ?? $this->document->template->key
                ?? config('cv-templates.renderer.fallback'),
            'is_update_mode' => $this->isUpdateMode,
            'document' => $this->document->toArray(),
            'preview' => $this->preview,
            'template_catalog' => $this->templateCatalog,
            'sections' => config('cv.sections'),
            'templates' => config('cv.templates'),
            'autosave_debounce_ms' => config('cv.autosave.debounce_ms'),
            'preview_debounce_ms' => config('cv.preview.debounce_ms', 200),
        ];
    }
}
