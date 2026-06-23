<?php

namespace App\Filament\Resources;

use App\Enums\PermissionName;
use App\Filament\Resources\PermissionResource\Pages;
use App\Support\Permissions\PermissionGrouper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationGroup = 'Admin';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Permissions';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->helperText('Use dot notation, e.g. users.view'),
            Forms\Components\Hidden::make('guard_name')
                ->default(config('permissions.guard', 'web')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->formatStateUsing(fn (string $state) => PermissionName::tryFrom($state)?->label() ?? $state)
                    ->description(fn (Permission $record) => PermissionGrouper::moduleFor($record->name) ?? 'Custom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guard_name')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('roles_count')
                    ->counts('roles')
                    ->label('Roles'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->defaultSort('name')
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label('Module')
                    ->getTitleFromRecordUsing(fn (Permission $record) => PermissionGrouper::moduleFor($record->name) ?? 'Custom')
                    ->collapsible(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('module')
                    ->label('Module')
                    ->options(fn () => collect(PermissionGrouper::modules())->keys()->mapWithKeys(fn ($k) => [$k => $k])->all())
                    ->query(function (Builder $query, array $data) {
                        $module = $data['value'] ?? null;

                        if (! $module) {
                            return $query;
                        }

                        $names = PermissionGrouper::modules()[$module] ?? [];

                        return $query->whereIn('name', $names);
                    }),
            ])
            ->actions([
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
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can(PermissionName::PermissionsManage->value) ?? false;
    }
}
