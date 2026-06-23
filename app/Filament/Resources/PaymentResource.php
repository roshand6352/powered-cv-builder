<?php

namespace App\Filament\Resources;

use App\Enums\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationGroup = 'SaaS';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')->relationship('user', 'name')->searchable()->required(),
            Forms\Components\Select::make('plan_id')->relationship('plan', 'name')->searchable(),
            Forms\Components\Select::make('gateway')
                ->options(collect(PaymentGateway::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]))
                ->required(),
            Forms\Components\TextInput::make('reference')->maxLength(255),
            Forms\Components\TextInput::make('transaction_id')->required(),
            Forms\Components\TextInput::make('amount')->numeric()->prefix('R')->required(),
            Forms\Components\Select::make('status')
                ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()]))
                ->required(),
        ])->columns(2);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Payment')->schema([
                Infolists\Components\TextEntry::make('reference'),
                Infolists\Components\TextEntry::make('transaction_id'),
                Infolists\Components\TextEntry::make('user.name'),
                Infolists\Components\TextEntry::make('plan.name'),
                Infolists\Components\TextEntry::make('amount')->money('ZAR'),
                Infolists\Components\TextEntry::make('gateway'),
                Infolists\Components\TextEntry::make('status')->badge(),
                Infolists\Components\TextEntry::make('created_at')->dateTime(),
            ])->columns(2),
            Infolists\Components\Section::make('Payload')
                ->schema([
                    Infolists\Components\TextEntry::make('payload')
                        ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT))
                        ->columnSpanFull(),
                ])
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('plan.name')->badge(),
                Tables\Columns\TextColumn::make('amount')->money('ZAR')->sortable(),
                Tables\Columns\TextColumn::make('gateway')->badge(),
                Tables\Columns\TextColumn::make('status')->badge()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(PaymentStatus::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),
                Tables\Filters\SelectFilter::make('gateway')
                    ->options(collect(PaymentGateway::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])),
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
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('markCompleted')
                        ->label('Mark Completed')
                        ->icon('heroicon-o-check-badge')
                        ->action(fn ($records) => $records->each->update(['status' => PaymentStatus::Completed])),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
