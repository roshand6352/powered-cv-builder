<?php

namespace App\Filament\Resources\AiLogResource\Pages;

use App\Filament\Resources\AiLogResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAiLog extends ViewRecord
{
    protected static string $resource = AiLogResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->record->loadMissing(['user', 'cv', 'coverLetter']);
    }
}
