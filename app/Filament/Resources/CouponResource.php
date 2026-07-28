<?php

namespace App\Filament\Resources;

use App\Enums\CouponStatus;
use App\Enums\CouponType;
use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationLabel = 'Coupons';

    protected static ?string $modelLabel = 'Coupon';

    protected static ?string $pluralModelLabel = 'Coupons';

    protected static ?string $navigationGroup = 'SaaS';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Coupon details')->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Coupon Code')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true)
                    ->dehydrateStateUsing(fn (?string $state) => strtoupper(trim((string) $state)))
                    ->disabled(fn (?Coupon $record) => $record !== null && (int) $record->used_count > 0)
                    ->dehydrated()
                    ->helperText('Unique code. Stored in uppercase.'),
                Forms\Components\Select::make('discount_type')
                    ->label('Discount Type')
                    ->options(CouponType::options())
                    ->required()
                    ->live()
                    ->native(false),
                Forms\Components\TextInput::make('discount_value')
                    ->label('Discount Value')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->rule(function (Get $get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get): void {
                            if ((float) $value < 0) {
                                $fail('Discount value cannot be negative.');
                            }

                            if ($get('discount_type') === CouponType::Percentage->value && (float) $value > 100) {
                                $fail('Percentage cannot exceed 100%.');
                            }
                        };
                    })
                    ->step('0.01')
                    ->suffix(fn (Get $get) => $get('discount_type') === CouponType::Percentage->value ? '%' : 'R'),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Expiry Date')
                    ->native(false)
                    ->rule(function (?Coupon $record) {
                        return function (string $attribute, $value, \Closure $fail) use ($record): void {
                            if (! $value) {
                                return;
                            }

                            if ($record === null && now()->greaterThan($value)) {
                                $fail('Expiry date cannot be in the past when creating.');
                            }
                        };
                    }),
                Forms\Components\TextInput::make('max_uses')
                    ->label('Max Uses')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->helperText('Must be greater than zero.'),
                Forms\Components\Select::make('status')
                    ->options(CouponStatus::options())
                    ->required()
                    ->native(false)
                    ->default(CouponStatus::Active->value),
                Forms\Components\TextInput::make('used_count')
                    ->label('Used Count')
                    ->disabled()
                    ->dehydrated(false)
                    ->visibleOn('edit')
                    ->helperText('Updated automatically after successful payments.'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('discount_type')
                    ->label('Discount Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof CouponType ? $state->label() : (string) $state)
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_value')
                    ->label('Discount Value')
                    ->sortable()
                    ->formatStateUsing(function ($state, Coupon $record): string {
                        $value = number_format((float) $state, 2);

                        return $record->discount_type === CouponType::Percentage
                            ? "{$value}%"
                            : "R {$value}";
                    }),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expiry Date')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('max_uses')
                    ->label('Max Uses')
                    ->sortable(),
                Tables\Columns\TextColumn::make('used_count')
                    ->label('Used Count')
                    ->sortable(),
                Tables\Columns\TextColumn::make('displayStatus')
                    ->label('Status')
                    ->badge()
                    ->color(fn (Coupon $record): string => $record->displayStatusColor())
                    ->getStateUsing(fn (Coupon $record): string => $record->displayStatus()),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Active')
                    ->query(fn (Builder $query) => $query
                        ->where('status', CouponStatus::Active)
                        ->where(fn (Builder $q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                        ->where(fn (Builder $q) => $q->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses'))),
                Tables\Filters\Filter::make('inactive')
                    ->label('Inactive')
                    ->query(fn (Builder $query) => $query->where('status', CouponStatus::Inactive)),
                Tables\Filters\Filter::make('expired')
                    ->label('Expired')
                    ->query(fn (Builder $query) => $query->whereNotNull('expires_at')->where('expires_at', '<=', now())),
                Tables\Filters\Filter::make('percentage')
                    ->label('Percentage')
                    ->query(fn (Builder $query) => $query->where('discount_type', CouponType::Percentage)),
                Tables\Filters\Filter::make('fixed')
                    ->label('Fixed')
                    ->query(fn (Builder $query) => $query->where('discount_type', CouponType::Fixed)),
                Tables\Filters\Filter::make('fully_used')
                    ->label('Fully Used')
                    ->query(fn (Builder $query) => $query
                        ->whereNotNull('max_uses')
                        ->whereColumn('used_count', '>=', 'max_uses')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Coupon $record): bool => $record->status !== CouponStatus::Active)
                    ->requiresConfirmation()
                    ->action(fn (Coupon $record) => $record->update(['status' => CouponStatus::Active])),
                Tables\Actions\Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-no-symbol')
                    ->color('gray')
                    ->visible(fn (Coupon $record): bool => $record->status === CouponStatus::Active)
                    ->requiresConfirmation()
                    ->action(fn (Coupon $record) => $record->update(['status' => CouponStatus::Inactive])),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (Coupon $record): bool => $record->canBeDeleted())
                    ->disabled(fn (Coupon $record): bool => ! $record->canBeDeleted()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->using(function ($records): void {
                            $records
                                ->filter(fn (Model $record) => $record instanceof Coupon && $record->canBeDeleted())
                                ->each->delete();
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
