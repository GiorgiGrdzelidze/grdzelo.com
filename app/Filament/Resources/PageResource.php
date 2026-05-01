<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatableSchema;
use App\Filament\Concerns\TranslationCompleteness;
use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Tabs::make('Page')->tabs([
                Schemas\Components\Tabs\Tab::make('Translations')->schema([
                    TranslatableSchema::tabs(fn (string $locale, bool $isDefault) => [
                        Forms\Components\TextInput::make("title.{$locale}")
                            ->label('Title')
                            ->required($isDefault)
                            ->maxLength(255),
                        Forms\Components\TextInput::make("slug.{$locale}")
                            ->label('Slug')
                            ->maxLength(255)
                            ->unique(table: 'pages', column: "slug->{$locale}", ignoreRecord: true)
                            ->placeholder('Auto-generated from title if blank'),
                        Forms\Components\Textarea::make("summary.{$locale}")
                            ->label('Summary')
                            ->maxLength(500)
                            ->rows(3),
                        Forms\Components\Textarea::make("excerpt.{$locale}")
                            ->label('Excerpt')
                            ->maxLength(500)
                            ->rows(2),
                        Forms\Components\RichEditor::make("body.{$locale}")
                            ->label('Body')
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('pages'),
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
                        Forms\Components\Toggle::make('nav_visible')
                            ->label('Show in Navigation'),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ]),
                ]),
                Schemas\Components\Tabs\Tab::make('Media')->schema([
                    Forms\Components\FileUpload::make('featured_image')
                        ->image()
                        ->directory('pages')
                        ->imageEditor(),
                    Forms\Components\FileUpload::make('gallery')
                        ->image()
                        ->multiple()
                        ->directory('pages/gallery')
                        ->reorderable(),
                ]),
                Schemas\Components\Tabs\Tab::make('CTA')->schema([
                    Forms\Components\TextInput::make('cta_text')
                        ->label('CTA Text')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('cta_url')
                        ->label('CTA URL')
                        ->url()
                        ->maxLength(255),
                ]),
                Schemas\Components\Tabs\Tab::make('SEO')->schema([
                    TranslatableSchema::seoTabs(),
                    Schemas\Components\Section::make('Indexing')->schema([
                        Forms\Components\TextInput::make('meta_keywords')->maxLength(255),
                        Schemas\Components\Grid::make(2)->schema([
                            Forms\Components\Toggle::make('noindex'),
                            Forms\Components\Toggle::make('nofollow'),
                        ]),
                        Forms\Components\TextInput::make('breadcrumb_title')->maxLength(255),
                    ]),
                    Schemas\Components\Section::make('Open Graph media')->schema([
                        Forms\Components\FileUpload::make('og_image')
                            ->label('OG Image')
                            ->image()
                            ->directory('seo'),
                        Forms\Components\Select::make('og_type')
                            ->options([
                                'website' => 'Website',
                                'article' => 'Article',
                                'profile' => 'Profile',
                            ])
                            ->default('website'),
                    ]),
                    Schemas\Components\Section::make('Twitter media')->schema([
                        Forms\Components\FileUpload::make('twitter_image')
                            ->image()
                            ->directory('seo'),
                        Forms\Components\Select::make('twitter_card')
                            ->options([
                                'summary' => 'Summary',
                                'summary_large_image' => 'Summary Large Image',
                            ])
                            ->default('summary_large_image'),
                    ]),
                    Schemas\Components\Section::make('Structured Data')->schema([
                        Forms\Components\Textarea::make('schema_json')
                            ->label('JSON-LD Schema')
                            ->rows(6)
                            ->helperText('Custom JSON-LD structured data'),
                    ]),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TranslationCompleteness::column('title'),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                    ]),
                Tables\Columns\IconColumn::make('nav_visible')
                    ->boolean()
                    ->label('In Nav'),
                Tables\Columns\TextColumn::make('publish_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
            ])
            ->recordActions([
                Actions\EditAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
