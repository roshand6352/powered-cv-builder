<?php

namespace App\Filament\Resources;

use App\Enums\BlogPostStatus;
use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Blog Posts';

    protected static ?string $modelLabel = 'Blog Post';

    protected static ?string $pluralModelLabel = 'Blog Posts';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Post')->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state, ?BlogPost $record): void {
                        if ($record?->exists) {
                            return;
                        }

                        $set('slug', Str::slug($state ?? ''));
                    }),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Auto-generated from the title; you can edit it.'),
                Forms\Components\TextInput::make('author')
                    ->required()
                    ->maxLength(255)
                    ->default(fn () => auth()->user()?->name ?? 'ApplyDocs ZA'),
                Forms\Components\Select::make('status')
                    ->options(BlogPostStatus::options())
                    ->required()
                    ->native(false)
                    ->default(BlogPostStatus::Draft->value),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Published Date')
                    ->seconds(false),
                Forms\Components\Textarea::make('excerpt')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('content')
                    ->required()
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'strike',
                        'blockquote',
                        'bulletList',
                        'orderedList',
                        'h2',
                        'h3',
                        'link',
                        'redo',
                        'undo',
                    ]),
                Forms\Components\FileUpload::make('featured_image')
                    ->label('Featured Image')
                    ->image()
                    ->disk('public')
                    ->directory('blog')
                    ->visibility('public')
                    ->imageEditor()
                    ->maxSize(4096)
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('SEO')->schema([
                Forms\Components\TextInput::make('meta_title')
                    ->maxLength(255)
                    ->helperText('Defaults to the post title when empty.'),
                Forms\Components\Textarea::make('meta_description')
                    ->rows(3)
                    ->helperText('Defaults to the excerpt when empty.'),
            ])->columns(1)->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Image')
                    ->disk('public')
                    ->square()
                    ->defaultImageUrl(asset('images/og-default.jpg')),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->wrap(),
                Tables\Columns\TextColumn::make('author')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (BlogPostStatus|string|null $state): string => $state instanceof BlogPostStatus
                        ? $state->label()
                        : BlogPostStatus::tryFrom((string) $state)?->label() ?? (string) $state)
                    ->color(fn (BlogPostStatus|string|null $state): string => match ($state instanceof BlogPostStatus ? $state : BlogPostStatus::tryFrom((string) $state)) {
                        BlogPostStatus::Published => 'success',
                        BlogPostStatus::Draft => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(BlogPostStatus::options()),
                Tables\Filters\Filter::make('published')
                    ->label('Published')
                    ->query(fn ($query) => $query->published()),
                Tables\Filters\Filter::make('draft')
                    ->label('Draft')
                    ->query(fn ($query) => $query->where('status', BlogPostStatus::Draft)),
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
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
