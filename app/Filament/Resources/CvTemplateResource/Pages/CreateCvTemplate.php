<?php

namespace App\Filament\Resources\CvTemplateResource\Pages;

use App\Filament\Resources\CvTemplateResource;
use App\Filament\Resources\CvTemplateResource\Concerns\NormalizesTemplateLayout;
use Filament\Resources\Pages\CreateRecord;

class CreateCvTemplate extends CreateRecord
{
    use NormalizesTemplateLayout;

    protected static string $resource = CvTemplateResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->normalizeLayoutPayload($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->normalizeLayoutPayload($data);
    }
}
