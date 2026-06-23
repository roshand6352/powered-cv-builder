<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Models\Plan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationGroup = 'SaaS';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Basic Information')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Plan name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('type_label')
                    ->label('Plan type label')
                    ->placeholder('FREE / PRO')
                    ->helperText('Shown on the pricing card tier label. Defaults to uppercase plan name.'),
                Forms\Components\TextInput::make('price')
                    ->label('Price amount')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('currency_symbol')
                    ->label('Currency symbol')
                    ->default('R')
                    ->maxLength(5),
                Forms\Components\TextInput::make('currency')
                    ->default('ZAR')
                    ->maxLength(3)
                    ->helperText('Internal currency code used for billing.'),
                Forms\Components\TextInput::make('price_suffix')
                    ->label('Price text / duration')
                    ->placeholder('/ forever free')
                    ->helperText('Example: / forever free, once-off · from'),
                Forms\Components\Textarea::make('description')
                    ->label('Short description')
                    ->rows(2)
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Plan Badge')->schema([
                Forms\Components\Toggle::make('show_badge')
                    ->label('Show popular badge'),
                Forms\Components\TextInput::make('badge_text')
                    ->label('Badge text')
                    ->placeholder('MOST POPULAR'),
            ])->columns(2),

            Forms\Components\Section::make('Features')->schema([
                Forms\Components\Repeater::make('features')
                    ->label('Feature list')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Feature title')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'disabled' => 'Disabled / Greyed out',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\Select::make('icon_type')
                            ->label('Feature icon type')
                            ->options([
                                'check' => 'Check',
                                'cross' => 'Cross',
                                'star' => 'Star',
                            ])
                            ->default('check')
                            ->required(),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(2)
                    ->orderColumn('sort_order')
                    ->collapsible()
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Button')->schema([
                Forms\Components\TextInput::make('button_text')
                    ->label('Button text')
                    ->placeholder('Get started free'),
                Forms\Components\TextInput::make('button_link')
                    ->label('Button URL')
                    ->placeholder('register, pricing, /contact, or full URL')
                    ->helperText('Leave blank for checkout (logged in) or registration (guest).'),
                Forms\Components\Select::make('button_style')
                    ->label('Button style type')
                    ->options([
                        'auto' => 'Auto (primary when highlighted)',
                        'primary' => 'Primary',
                        'secondary' => 'Secondary',
                        'outline' => 'Outline',
                    ])
                    ->default('auto')
                    ->required(),
            ])->columns(3),

            Forms\Components\Section::make('Footer Note')->schema([
                Forms\Components\Textarea::make('footer_note')
                    ->label('Footer note text')
                    ->rows(2)
                    ->placeholder('Free downloads include an ApplyDocs ZA watermark')
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Payment Information')->schema([
                Forms\Components\Toggle::make('show_payment_methods')
                    ->label('Show payment methods'),
                Forms\Components\Repeater::make('payment_methods')
                    ->label('Payment labels')
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->required()
                            ->placeholder('Card'),
                    ])
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Design Settings')->schema([
                Forms\Components\ColorPicker::make('card_border_color')
                    ->label('Card border color'),
                Forms\Components\ColorPicker::make('card_background_color')
                    ->label('Card background color'),
                Forms\Components\ColorPicker::make('button_background_color')
                    ->label('Button background color'),
                Forms\Components\ColorPicker::make('button_text_color')
                    ->label('Button text color'),
                Forms\Components\ColorPicker::make('title_color')
                    ->label('Title color'),
                Forms\Components\ColorPicker::make('price_color')
                    ->label('Price color'),
                Forms\Components\ColorPicker::make('badge_color')
                    ->label('Badge color'),
                Forms\Components\ColorPicker::make('feature_text_color')
                    ->label('Feature text color'),
            ])->columns(2),

            Forms\Components\Section::make('Visibility')->schema([
                Forms\Components\Select::make('status')
                    ->label('Plan status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Display order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first.'),
                Forms\Components\Toggle::make('show_on_website')
                    ->label('Show on website')
                    ->default(true),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Highlight plan'),
            ])->columns(2),

            Forms\Components\Section::make('Subscription Settings')
                ->description('Backend billing and access limits (not shown on the pricing card).')
                ->schema([
                    Forms\Components\TextInput::make('ai_credits_limit')
                        ->numeric()
                        ->required(),
                    Forms\Components\TextInput::make('duration_hours')
                        ->numeric()
                        ->label('Duration (hours)'),
                    Forms\Components\TextInput::make('duration_days')
                        ->numeric()
                        ->label('Duration (days)'),
                    Forms\Components\Toggle::make('is_lifetime')
                        ->label('Lifetime access'),
                    Forms\Components\Toggle::make('has_watermark')
                        ->default(true),
                    Forms\Components\TextInput::make('billing_interval')
                        ->default('monthly'),
                ])
                ->columns(3)
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type_label')->label('Type')->toggleable(),
                Tables\Columns\TextColumn::make('slug')->searchable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('price')->money('ZAR')->sortable(),
                Tables\Columns\IconColumn::make('show_on_website')->boolean()->label('Website'),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Highlight'),
                Tables\Columns\IconColumn::make('show_badge')->boolean()->label('Badge'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ]),
                Tables\Filters\TernaryFilter::make('show_on_website')->label('Show on website'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Highlighted'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['status' => 'active'])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn ($records) => $records->each->update(['status' => 'inactive'])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
