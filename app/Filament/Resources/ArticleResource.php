<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatableSchema;
use App\Filament\Concerns\TranslationCompleteness;
use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Tabs::make('Article')->tabs([
                Schemas\Components\Tabs\Tab::make('Translations')->schema([
                    TranslatableSchema::tabs(fn (string $locale, bool $isDefault) => [
                        Forms\Components\TextInput::make("title.{$locale}")
                            ->label('Title')
                            ->required($isDefault)
                            ->maxLength(255),
                        Forms\Components\TextInput::make("slug.{$locale}")
                            ->label('Slug')
                            ->maxLength(255)
                            ->unique(table: 'articles', column: "slug->{$locale}", ignoreRecord: true)
                            ->placeholder('Auto-generated from title if blank'),
                        Forms\Components\Textarea::make("excerpt.{$locale}")
                            ->label('Excerpt')
                            ->maxLength(500)
                            ->rows(3),
                        Forms\Components\RichEditor::make("body.{$locale}")
                            ->label('Body')
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('articles'),
                    ])->columnSpanFull(),
                ]),
                Schemas\Components\Tabs\Tab::make('Settings')->schema([
                    Schemas\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\DateTimePicker::make('publish_at')
                            ->label('Publish Date'),
                    ]),
                    Schemas\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('article_category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('slug')->required(),
                            ]),
                        Forms\Components\TextInput::make('reading_time')
                            ->numeric()
                            ->suffix('min')
                            ->helperText('Auto-calculated if left blank'),
                    ]),
                    Schemas\Components\Grid::make(2)->schema([
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured Article'),
                        Forms\Components\Select::make('user_id')
                            ->relationship('author', 'name')
                            ->default(auth()->id())
                            ->label('Author'),
                    ]),
                ]),
                Schemas\Components\Tabs\Tab::make('Media')->schema([
                    Forms\Components\FileUpload::make('cover_image')
                        ->image()
                        ->directory('articles')
                        ->imageEditor(),
                ]),
                Schemas\Components\Tabs\Tab::make('Tags')->schema([
                    Forms\Components\Select::make('tags')
                        ->relationship('tags', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')->required(),
                        ]),
                ]),
                Schemas\Components\Tabs\Tab::make('SEO')->schema([
                    Schemas\Components\Section::make('Search Engine Optimization')->schema([
                        Forms\Components\TextInput::make('meta_title')->maxLength(255),
                        Forms\Components\Textarea::make('meta_description')->maxLength(500)->rows(3),
                        Forms\Components\TextInput::make('canonical_url')->url()->maxLength(255),
                        Schemas\Components\Grid::make(2)->schema([
                            Forms\Components\Toggle::make('noindex'),
                            Forms\Components\Toggle::make('nofollow'),
                        ]),
                        Forms\Components\TextInput::make('breadcrumb_title')->maxLength(255),
                    ]),
                    Schemas\Components\Section::make('Open Graph')->schema([
                        Forms\Components\TextInput::make('og_title')->maxLength(255),
                        Forms\Components\Textarea::make('og_description')->maxLength(500)->rows(2),
                        Forms\Components\FileUpload::make('og_image')->image()->directory('seo'),
                    ]),
                    Schemas\Components\Section::make('Twitter')->schema([
                        Forms\Components\TextInput::make('twitter_title')->maxLength(255),
                        Forms\Components\Textarea::make('twitter_description')->maxLength(500)->rows(2),
                        Forms\Components\FileUpload::make('twitter_image')->image()->directory('seo'),
                    ]),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')->circular()->label(''),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                TranslationCompleteness::column('title'),
                Tables\Columns\TextColumn::make('category.name')->sortable()->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['warning' => 'draft', 'success' => 'published']),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\TextColumn::make('publish_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('publish_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published']),
                Tables\Filters\SelectFilter::make('article_category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
            ])
            ->recordActions([Actions\EditAction::make()])
            ->toolbarActions([
                Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
