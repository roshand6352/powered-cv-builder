<?php

namespace App\Filament\Resources\Concerns;

use App\Support\CoverLetter\CoverLetterTemplateLayout;
use App\Support\Cv\CvTemplateLayout;
use Filament\Forms;

trait InteractsWithTemplateDesignerForms
{
    /**
     * @return array<int, Forms\Components\Component>
     */
    protected static function sharedColorSettingsSchema(array $defaults): array
    {
        return [
            Forms\Components\ColorPicker::make('layout_json.settings.primary_color')
                ->label('Primary Color')
                ->default($defaults['primary_color']),
            Forms\Components\ColorPicker::make('layout_json.settings.secondary_color')
                ->label('Secondary Color')
                ->default($defaults['secondary_color']),
            Forms\Components\ColorPicker::make('layout_json.settings.background_color')
                ->label('Background Color')
                ->default($defaults['background_color']),
            Forms\Components\ColorPicker::make('layout_json.settings.text_color')
                ->label('Text Color')
                ->default($defaults['text_color']),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    protected static function sharedTypographySchema(array $defaults): array
    {
        return [
            Forms\Components\Select::make('layout_json.settings.font')
                ->label('Font Family')
                ->options(CvTemplateLayout::fontOptions())
                ->default($defaults['font'])
                ->required()
                ->searchable()
                ->native(false),
            Forms\Components\TextInput::make('layout_json.settings.typography.font_size')
                ->label('Base Font Size')
                ->default($defaults['typography']['font_size'])
                ->placeholder('14px'),
            Forms\Components\Select::make('layout_json.settings.typography.font_weight')
                ->label('Font Weight')
                ->options([
                    '400' => 'Regular (400)',
                    '500' => 'Medium (500)',
                    '600' => 'Semi Bold (600)',
                    '700' => 'Bold (700)',
                ])
                ->default($defaults['typography']['font_weight'])
                ->native(false),
            Forms\Components\Select::make('layout_json.settings.typography.heading_weight')
                ->label('Heading Weight')
                ->options([
                    '600' => 'Semi Bold (600)',
                    '700' => 'Bold (700)',
                    '800' => 'Extra Bold (800)',
                ])
                ->default($defaults['typography']['heading_weight'])
                ->native(false),
            Forms\Components\Select::make('layout_json.settings.typography.heading_style')
                ->label('Heading Style')
                ->options(CvTemplateLayout::headingStyleOptions())
                ->default($defaults['typography']['heading_style'])
                ->native(false),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    protected static function sharedSpacingSchema(array $defaults): array
    {
        return [
            Forms\Components\Select::make('layout_json.settings.spacing.page_padding')
                ->label('Page Padding')
                ->options(CvTemplateLayout::spacingPresetOptions())
                ->default($defaults['spacing']['page_padding'])
                ->native(false),
            Forms\Components\Select::make('layout_json.settings.spacing.section_gap')
                ->label('Section Gap')
                ->options(CvTemplateLayout::spacingPresetOptions())
                ->default($defaults['spacing']['section_gap'])
                ->native(false),
            Forms\Components\Select::make('layout_json.settings.spacing.item_gap')
                ->label('Item Gap')
                ->options([
                    '0.75rem' => 'Compact',
                    '1.1rem' => 'Normal',
                    '1.5rem' => 'Relaxed',
                ])
                ->default($defaults['spacing']['item_gap'])
                ->native(false),
            Forms\Components\Select::make('layout_json.settings.borders.style')
                ->label('Border Style')
                ->options(CvTemplateLayout::borderStyleOptions())
                ->default($defaults['borders']['style'])
                ->native(false),
            Forms\Components\TextInput::make('layout_json.settings.borders.width')
                ->label('Border Width')
                ->default($defaults['borders']['width'])
                ->placeholder('2px'),
            Forms\Components\TextInput::make('layout_json.settings.borders.radius')
                ->label('Border Radius')
                ->default($defaults['borders']['radius'])
                ->placeholder('0px'),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function cvDesignerFormSchema(): array
    {
        $defaults = CvTemplateLayout::defaults();

        return [
            Forms\Components\Section::make('Layout Type')
                ->description('Visual layout engine variant. Templates are fully managed from admin.')
                ->schema([
                    Forms\Components\Select::make('layout_json.layout')
                        ->label('Layout Type')
                        ->options(CvTemplateLayout::layoutTypeOptions())
                        ->default($defaults['layout'])
                        ->required()
                        ->searchable()
                        ->native(false),
                ]),
            Forms\Components\Section::make('Layout Positions')
                ->schema([
                    Forms\Components\Select::make('layout_json.settings.sidebar_position')
                        ->label('Sidebar Position')
                        ->options(CvTemplateLayout::sidebarPositionOptions())
                        ->default($defaults['settings']['sidebar_position'])
                        ->required()
                        ->native(false),
                    Forms\Components\Select::make('layout_json.settings.photo_position')
                        ->label('Photo Position')
                        ->options(CvTemplateLayout::photoPositionOptions())
                        ->default($defaults['settings']['photo_position'])
                        ->required()
                        ->native(false),
                ])->columns(2),
            Forms\Components\Section::make('Color Settings')
                ->schema(static::sharedColorSettingsSchema($defaults['settings']))
                ->columns(2),
            Forms\Components\Section::make('Color Settings (Sidebar)')
                ->schema([
                    Forms\Components\ColorPicker::make('layout_json.settings.sidebar_background_color')
                        ->label('Sidebar Background Color')
                        ->default($defaults['settings']['sidebar_background_color']),
                ]),
            Forms\Components\Section::make('Typography Settings')
                ->schema(static::sharedTypographySchema($defaults['settings']))
                ->columns(3),
            Forms\Components\Section::make('Spacing & Borders')
                ->schema(static::sharedSpacingSchema($defaults['settings']))
                ->columns(3),
            Forms\Components\Section::make('Sections & Icons')
                ->schema([
                    Forms\Components\Toggle::make('layout_json.settings.icons.enabled')
                        ->label('Show Section Icons')
                        ->default($defaults['settings']['icons']['enabled']),
                    Forms\Components\TagsInput::make('layout_json.section_order')
                        ->label('Section Order')
                        ->suggestions(array_keys(CvTemplateLayout::sectionOptions()))
                        ->reorderable()
                        ->default($defaults['section_order'])
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\CheckboxList::make('layout_json.enabled_sections')
                        ->label('Section Visibility')
                        ->options(CvTemplateLayout::sectionOptions())
                        ->default($defaults['enabled_sections'])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function coverLetterDesignerFormSchema(): array
    {
        $defaults = CoverLetterTemplateLayout::defaults();

        return [
            Forms\Components\Section::make('Layout')
                ->description('Cover letter page structure and header presentation.')
                ->schema([
                    Forms\Components\Select::make('layout_json.layout')
                        ->label('Layout Type')
                        ->options(CoverLetterTemplateLayout::layoutTypeOptions())
                        ->default($defaults['layout'])
                        ->required()
                        ->native(false),
                    Forms\Components\Select::make('layout_json.settings.header_style')
                        ->label('Header Style')
                        ->options(CoverLetterTemplateLayout::headerStyleOptions())
                        ->default($defaults['settings']['header_style'])
                        ->required()
                        ->native(false),
                ])->columns(2),
            Forms\Components\Section::make('Color Settings')
                ->schema(static::sharedColorSettingsSchema($defaults['settings']))
                ->columns(2),
            Forms\Components\Section::make('Typography Settings')
                ->schema(static::sharedTypographySchema($defaults['settings']))
                ->columns(3),
            Forms\Components\Section::make('Spacing & Borders')
                ->schema(static::sharedSpacingSchema($defaults['settings']))
                ->columns(3),
            Forms\Components\Section::make('Cover Letter Sections')
                ->description('Manage which sections appear and their display order.')
                ->schema([
                    Forms\Components\TagsInput::make('layout_json.section_order')
                        ->label('Section Order')
                        ->suggestions(CoverLetterTemplateLayout::sectionKeys())
                        ->reorderable()
                        ->default($defaults['section_order'])
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\CheckboxList::make('layout_json.enabled_sections')
                        ->label('Section Visibility')
                        ->options(CoverLetterTemplateLayout::sectionOptions())
                        ->default($defaults['enabled_sections'])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),
        ];
    }
}
