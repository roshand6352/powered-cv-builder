<?php

namespace App\Filament\Resources;

use App\Enums\TemplateType;
use App\Filament\Resources\Concerns\InteractsWithTemplateDesignerForms;
use App\Filament\Resources\CvTemplateResource\Pages;
use App\Models\CvTemplate;
use App\Support\Cv\CvTemplateLayout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CvTemplateResource extends Resource
{
    use InteractsWithTemplateDesignerForms;

    protected static ?string $model = CvTemplate::class;

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Templates';

    protected static ?string $modelLabel = 'Template';

    protected static ?string $pluralModelLabel = 'Templates';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Template')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Details')
                        ->icon('heroicon-o-document-text')
                        ->schema(static::detailsFormSchema()),
                    Forms\Components\Tabs\Tab::make('CV Designer')
                        ->icon('heroicon-o-paint-brush')
                        ->schema(static::cvDesignerFormSchema())
                        ->visible(fn (Get $get): bool => static::formTypeIs($get, TemplateType::Cv)),
                    Forms\Components\Tabs\Tab::make('Cover Letter Designer')
                        ->icon('heroicon-o-envelope')
                        ->schema(static::coverLetterDesignerFormSchema())
                        ->visible(fn (Get $get): bool => static::formTypeIs($get, TemplateType::CoverLetter)),
                ])
                ->columnSpanFull(),
        ]);
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function detailsFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Template Details')->schema([
                Forms\Components\Select::make('type')
                    ->label('Template Type')
                    ->options(TemplateType::options())
                    ->default(TemplateType::Cv->value)
                    ->required()
                    ->native(false)
                    ->live(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('status')
                    ->label('Active')
                    ->default(true)
                    ->formatStateUsing(fn ($state): bool => (int) $state === 1)
                    ->dehydrateStateUsing(fn (bool $state): int => $state ? 1 : 0),
            ])->columns(2),
        ];
    }

    /**
     * @deprecated Use cvDesignerFormSchema() or coverLetterDesignerFormSchema() instead.
     *
     * @return array<int, Forms\Components\Component>
     */
    public static function designerFormSchema(): array
    {
        return static::cvDesignerFormSchema();
    }

    public static function designerFormSchemaFor(CvTemplate $record): array
    {
        return $record->isCoverLetter()
            ? static::coverLetterDesignerFormSchema()
            : static::cvDesignerFormSchema();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (TemplateType $state): string => $state->label())
                    ->color(fn (TemplateType $state): string => $state->isCv() ? 'info' : 'warning')
                    ->sortable(),
                Tables\Columns\TextColumn::make('layout_json.layout')
                    ->label('Layout')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('layout_json.settings.sidebar_position')
                    ->label('Sidebar')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('status')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cvs_count')
                    ->counts('cvs')
                    ->label('CVs')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Template Type')
                    ->options(TemplateType::options()),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Active')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only')
                    ->queries(
                        true: fn ($query) => $query->where('status', 1),
                        false: fn ($query) => $query->where('status', 0),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('design')
                    ->label('Design')
                    ->icon('heroicon-o-paint-brush')
                    ->url(fn (CvTemplate $record): string => static::getUrl('design', ['record' => $record])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['status' => 1])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn ($records) => $records->each->update(['status' => 0])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCvTemplates::route('/'),
            'create' => Pages\CreateCvTemplate::route('/create'),
            'edit' => Pages\EditCvTemplate::route('/{record}/edit'),
            'design' => Pages\DesignCvTemplate::route('/{record}/design'),
        ];
    }

    protected static function formTypeIs(Get $get, TemplateType $type): bool
    {
        $value = $get('type');

        if ($value instanceof TemplateType) {
            return $value === $type;
        }

        return TemplateType::tryFrom((string) $value) === $type;
    }
}
