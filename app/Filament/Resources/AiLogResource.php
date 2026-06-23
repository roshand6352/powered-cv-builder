<?php

namespace App\Filament\Resources;

use App\Enums\AiFeature;
use App\Enums\AiLogStatus;
use App\Filament\Resources\AiLogResource\Pages;
use App\Models\AiLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AiLogResource extends Resource
{
    protected static ?string $model = AiLog::class;

    protected static ?string $navigationGroup = 'AI & Analytics';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'AI Logs';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')->relationship('user', 'name')->disabled(),
            Forms\Components\TextInput::make('feature')->disabled(),
            Forms\Components\Select::make('status')
                ->options(collect(AiLogStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]))
                ->disabled(),
            Forms\Components\TextInput::make('model')->disabled(),
            Forms\Components\TextInput::make('tokens_used')->numeric()->disabled(),
            Forms\Components\TextInput::make('credits_used')->numeric()->disabled(),
        ])->columns(2);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Request')->schema([
                Infolists\Components\TextEntry::make('user.name'),
                Infolists\Components\TextEntry::make('feature')
                    ->formatStateUsing(fn ($state) => AiFeature::tryFrom((string) $state)?->label() ?? $state)
                    ->badge(),
                Infolists\Components\TextEntry::make('status')
                    ->formatStateUsing(fn ($state) => $state instanceof AiLogStatus ? $state->label() : (string) ($state ?? ''))
                    ->badge(),
                Infolists\Components\TextEntry::make('model')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('tokens_used'),
                Infolists\Components\TextEntry::make('credits_used'),
                Infolists\Components\TextEntry::make('created_at')->dateTime(),
                Infolists\Components\TextEntry::make('completed_at')->dateTime()->placeholder('—'),
            ])->columns(2),
            Infolists\Components\Section::make('Result')->schema([
                Infolists\Components\TextEntry::make('result')
                    ->placeholder('—')
                    ->columnSpanFull(),
                Infolists\Components\TextEntry::make('error_message')
                    ->color('danger')
                    ->placeholder('—')
                    ->columnSpanFull(),
            ]),
            Infolists\Components\Section::make('Payload')->collapsed()->schema([
                Infolists\Components\TextEntry::make('request')
                    ->label('Request')
                    ->state(fn (AiLog $record): ?string => static::formatJsonState($record->request))
                    ->placeholder('—')
                    ->fontFamily(FontFamily::Mono)
                    ->copyable()
                    ->columnSpanFull(),
                Infolists\Components\TextEntry::make('response')
                    ->label('Response')
                    ->state(fn (AiLog $record): ?string => static::formatJsonState($record->response))
                    ->placeholder('—')
                    ->fontFamily(FontFamily::Mono)
                    ->copyable()
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('feature')
                    ->formatStateUsing(fn ($state) => AiFeature::tryFrom($state)?->label() ?? $state)
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn ($state) => $state instanceof AiLogStatus ? $state->label() : (string) ($state ?? ''))
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tokens_used')->sortable(),
                Tables\Columns\TextColumn::make('credits_used')->sortable(),
                Tables\Columns\TextColumn::make('model')->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('feature')
                    ->options(collect(AiFeature::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(AiLogStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function canCreate(): bool
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
        return parent::getEloquentQuery()->with('user');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiLogs::route('/'),
            'view' => Pages\ViewAiLog::route('/{record}'),
        ];
    }
}
