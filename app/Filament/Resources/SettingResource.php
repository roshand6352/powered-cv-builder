<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Settings';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('group')->options([
                'general' => 'General',
                'billing' => 'Billing',
                'ai' => 'AI',
                'email' => 'Email',
            ])->required(),
            Forms\Components\TextInput::make('key')->required()->unique(ignoreRecord: true),
            Forms\Components\Select::make('type')->options([
                'string' => 'String',
                'integer' => 'Integer',
                'boolean' => 'Boolean',
                'json' => 'JSON',
            ])->required(),
            Forms\Components\Textarea::make('value')->rows(4)->columnSpanFull(),
            Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')->badge()->sortable(),
                Tables\Columns\TextColumn::make('key')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('value')->limit(50)->wrap(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->defaultSort('group')
            ->groups([
                Tables\Grouping\Group::make('group')->label('Group'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')->options([
                    'general' => 'General',
                    'billing' => 'Billing',
                    'ai' => 'AI',
                    'email' => 'Email',
                ]),
                Tables\Filters\SelectFilter::make('type')->options([
                    'string' => 'String',
                    'integer' => 'Integer',
                    'boolean' => 'Boolean',
                    'json' => 'JSON',
                ]),
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
