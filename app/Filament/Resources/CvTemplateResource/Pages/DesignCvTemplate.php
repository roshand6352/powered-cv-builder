<?php

namespace App\Filament\Resources\CvTemplateResource\Pages;

use App\Filament\Resources\CvTemplateResource;
use App\Filament\Resources\CvTemplateResource\Concerns\NormalizesTemplateLayout;
use App\Support\CoverLetter\CoverLetterTemplateLayout;
use App\Support\Cv\CvTemplateLayout;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class DesignCvTemplate extends EditRecord
{
    use NormalizesTemplateLayout;

    protected static string $resource = CvTemplateResource::class;

    protected static ?string $title = 'Template Designer';

    protected static ?string $navigationLabel = 'Design';

    public function form(Form $form): Form
    {
        return $form->schema(CvTemplateResource::designerFormSchemaFor($this->record));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('editDetails')
                ->label('Template Details')
                ->icon('heroicon-o-document-text')
                ->url(fn () => CvTemplateResource::getUrl('edit', ['record' => $this->record])),
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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $layout = $record->isCoverLetter()
            ? CoverLetterTemplateLayout::normalize($data['layout_json'] ?? null)
            : CvTemplateLayout::normalize($data['layout_json'] ?? null);

        $record->update([
            'layout_json' => $layout,
        ]);

        return $record;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Template design saved';
    }
}
