<?php

namespace App\Filament\Resources;

use App\Enums\PermissionName;
use App\Enums\RoleName;
use App\Filament\Forms\PermissionCheckboxGroups;
use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationGroup = 'Admin';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Roles';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Role Details')->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->disabled(fn (?Role $record) => $record && in_array($record->name, RoleName::staffRoles(), true)),
                Forms\Components\Hidden::make('guard_name')
                    ->default(config('permissions.guard', 'web')),
            ])->columns(2),

            ...PermissionCheckboxGroups::schema(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->formatStateUsing(fn (string $state) => RoleName::tryFrom($state)?->label() ?? $state)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label('Permissions'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('guard_name')
                    ->options([
                        config('permissions.guard', 'web') => config('permissions.guard', 'web'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can(PermissionName::RolesManage->value) ?? false;
    }
}
