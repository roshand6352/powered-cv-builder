<?php

namespace App\Filament\Resources;

use App\Enums\SubscriptionStatus;
use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationGroup = 'SaaS';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')->relationship('user', 'name')->searchable()->required(),
            Forms\Components\Select::make('plan_id')->relationship('plan', 'name')->searchable()->required(),
            Forms\Components\Select::make('status')
                ->options(collect(SubscriptionStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]))
                ->required(),
            Forms\Components\DateTimePicker::make('starts_at'),
            Forms\Components\DateTimePicker::make('ends_at'),
            Forms\Components\DateTimePicker::make('cancelled_at'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('user.email')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('plan.name')->badge()->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('starts_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('ends_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(SubscriptionStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),
                Tables\Filters\SelectFilter::make('plan_id')
                    ->relationship('plan', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('markActive')
                        ->label('Mark Active')
                        ->icon('heroicon-o-check')
                        ->action(fn ($records) => $records->each->update(['status' => SubscriptionStatus::Active])),
                    Tables\Actions\BulkAction::make('markCancelled')
                        ->label('Mark Cancelled')
                        ->icon('heroicon-o-x-mark')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update([
                            'status' => SubscriptionStatus::Cancelled,
                            'cancelled_at' => now(),
                        ])),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user', 'plan']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'view' => Pages\ViewSubscription::route('/{record}'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
