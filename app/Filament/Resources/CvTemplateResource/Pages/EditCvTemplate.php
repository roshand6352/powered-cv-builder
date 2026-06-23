<?php

namespace App\Filament\Resources\CvTemplateResource\Pages;

use App\Filament\Resources\CvTemplateResource;
use App\Filament\Resources\CvTemplateResource\Concerns\NormalizesTemplateLayout;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCvTemplate extends EditRecord
{
    use NormalizesTemplateLayout;

    protected static string $resource = CvTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('design')
                ->label('Open Designer')
                ->icon('heroicon-o-paint-brush')
                ->url(fn () => CvTemplateResource::getUrl('design', ['record' => $this->record])),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $this->normalizeLayoutPayload($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->normalizeLayoutPayload($data);
    }
}
