<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiLogResource\Pages;
use App\Models\JobMatchScore;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AiLogResource extends Resource
{
    protected static ?string $model = JobMatchScore::class;

    protected static ?string $navigationGroup = 'AI & Analytics';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'AI Logs';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Analysis')->schema([
                Infolists\Components\TextEntry::make('user.name'),
                Infolists\Components\TextEntry::make('cv.title')->label('CV'),
                Infolists\Components\TextEntry::make('coverLetter.title')
                    ->label('Cover Letter')
                    ->placeholder('No Cover Letter'),
                Infolists\Components\TextEntry::make('score')
                    ->suffix('%')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('status')
                    ->badge(),
                Infolists\Components\TextEntry::make('created_at')->dateTime(),
            ])->columns(2),
            Infolists\Components\Section::make('Job Description')->schema([
                Infolists\Components\TextEntry::make('job_description')
                    ->placeholder('—')
                    ->columnSpanFull(),
            ]),
            Infolists\Components\Section::make('AI JSON Response')->schema([
                Infolists\Components\TextEntry::make('result_json')
                    ->label('Response')
                    ->state(fn (JobMatchScore $record): ?string => static::formatJsonState($record->result_json))
                    ->placeholder('—')
                    ->fontFamily(FontFamily::Mono)
                    ->copyable()
                    ->columnSpanFull(),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('cv.title')->label('CV')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('coverLetter.title')
                    ->label('Cover Letter')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('score')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    protected static function formatJsonState(mixed $state): ?string
    {
        if ($state === null || $state === '') {
            return null;
        }

        if (is_array($state) || is_object($state)) {
            $encoded = json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            return $encoded !== false ? $encoded : null;
        }

        return (string) $state;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'cv', 'coverLetter']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiLogs::route('/'),
            'view' => Pages\ViewAiLog::route('/{record}'),
        ];
    }
}
