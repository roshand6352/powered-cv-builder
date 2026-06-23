<?php

namespace App\Services\Cv;

use App\DTOs\Cv\CvDocumentData;
use App\Support\Cv\CvDocumentMigrator;
use App\Services\BaseService;
use App\Services\Cv\Templates\TemplateRegistry;

class CvTemplateApplicationService extends BaseService
{
    public function __construct(
        protected TemplateRegistry $templateRegistry,
    ) {}

    /**
     * Apply a template definition to an in-memory CV document (preview / switch).
     *
     * @return array{document: CvDocumentData, definition: array<string, mixed>, template_key: string}
     */
    public function applyToDocument(CvDocumentData $document, string $templateKey, bool $mergeSettings = false): array
    {
        $definition = $this->templateRegistry->resolve($templateKey) ?? [];

        $document->template->key = $templateKey;
        $document->template->slug = $templateKey;
        $document->template->id = $definition['id'] ?? $document->template->id;

        if ($mergeSettings) {
            $document->template->settings = $definition['settings'] ?? [];
        }

        if (! empty($definition['section_order'])) {
            $document->meta->sectionOrder = CvDocumentMigrator::migrateSectionKeys($definition['section_order']);
        }

        if (! empty($definition['enabled_sections'])) {
            $document->meta->enabledSections = CvDocumentMigrator::migrateSectionKeys($definition['enabled_sections']);
        }

        return [
            'document' => $document,
            'definition' => $definition,
            'template_key' => $templateKey,
        ];
    }
}
