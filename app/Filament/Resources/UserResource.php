<?php

namespace App\Filament\Resources;

use App\Enums\PermissionName;
use App\Enums\PlanSlug;
use App\Filament\Resources\UserResource\Pages;
use App\Models\Plan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'SaaS';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Profile')->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('email')->email()->required()->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn (?string $state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->required(fn (string $operation) => $operation === 'create')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('email_verified_at'),
            ])->columns(2),

            Forms\Components\Section::make('Subscription')->schema([
                Forms\Components\Select::make('plan_id')
                    ->label('Plan')
                    ->relationship('plan', 'name', fn ($query) => $query->catalog()->orderBy('sort_order'))
                    ->searchable()
                    ->preload(),
                Forms\Components\DateTimePicker::make('plan_expiry'),
                Forms\Components\TextInput::make('ai_usage_count')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
            ])->columns(3),

            Forms\Components\Section::make('Roles & Permissions')->schema([
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->options(fn () => Role::query()->pluck('name', 'name'))
                    ->helperText('Assign one or more roles. Permissions are inherited from roles.')
                    ->columnSpanFull(),
                Forms\Components\Placeholder::make('permission_summary')
                    ->label('Permission summary')
                    ->content(function (?User $record): string {
                        if (! $record) {
                            return 'Save the user and assign roles to see effective permissions.';
                        }

                        $permissions = $record->getAllPermissions()
                            ->pluck('name')
                            ->map(fn (string $name) => PermissionName::tryFrom($name)?->label() ?? $name)
                            ->sort()
                            ->values();

                        if ($permissions->isEmpty()) {
                            return 'No permissions assigned via roles.';
                        }

                        return $permissions->implode(' · ');
                    })
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->label('Roles'),
                Tables\Columns\TextColumn::make('plan.name')->label('Plan')->badge()->sortable(),
                Tables\Columns\TextColumn::make('ai_usage_count')->label('AI Used')->sortable(),
                Tables\Columns\TextColumn::make('plan_expiry')->dateTime()->sortable(),
                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn (User $record) => $record->email_verified_at !== null),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
                Tables\Filters\SelectFilter::make('plan_id')
                    ->label('Plan')
                    ->options(fn () => Plan::query()->catalog()->orderBy('sort_order')->pluck('name', 'id')->all()),
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Email verified')
                    ->nullable()
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('email_verified_at'),
                        false: fn (Builder $query) => $query->whereNull('email_verified_at'),
                    ),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('assignFreePlan')
                        ->label('Assign Free Plan')
                        ->icon('heroicon-o-arrow-down-circle')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $freePlan = Plan::query()->where('slug', PlanSlug::Free->value)->first();
                            if ($freePlan) {
                                $records->each->update(['plan_id' => $freePlan->id, 'plan_expiry' => null]);
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['plan', 'roles']);
    }
}
