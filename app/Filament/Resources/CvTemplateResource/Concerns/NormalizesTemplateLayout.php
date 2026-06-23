<?php

namespace App\Filament\Resources\CvTemplateResource\Concerns;

use App\Enums\TemplateType;
use App\Support\CoverLetter\CoverLetterTemplateLayout;
use App\Support\Cv\CvTemplateLayout;

trait NormalizesTemplateLayout
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function normalizeLayoutPayload(array $data): array
    {
        $type = $this->resolveTemplateType($data);

        $data['type'] = $type->value;
        $data['layout_json'] = $type->isCoverLetter()
            ? CoverLetterTemplateLayout::normalize($data['layout_json'] ?? null)
            : CvTemplateLayout::normalize($data['layout_json'] ?? null);

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function resolveTemplateType(array $data): TemplateType
    {
        $type = $data['type'] ?? null;

        if ($type instanceof TemplateType) {
            return $type;
        }

        if (isset($this->record) && $this->record instanceof \App\Models\CvTemplate) {
            return $this->record->type ?? TemplateType::Cv;
        }

        return TemplateType::tryFrom((string) $type) ?? TemplateType::Cv;
    }
}
