<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatableSchema;
use App\Filament\Concerns\TranslationCompleteness;
use App\Filament\Resources\AlbumResource\Pages;
use App\Models\Album;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AlbumResource extends Resource
{
    protected static ?string $model = Album::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Media';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Tabs::make('Album')->tabs([
                Schemas\Components\Tabs\Tab::make('Translations')->schema([
                    TranslatableSchema::tabs(fn (string $locale, bool $isDefault) => [
                        Forms\Components\TextInput::make("title.{$locale}")
                            ->label('Title')
                            ->required($isDefault)
                            ->maxLength(255),
                        Forms\Components\TextInput::make("slug.{$locale}")
                            ->label('Slug')
                            ->maxLength(255)
                            ->unique(table: 'albums', column: "slug->{$locale}", ignoreRecord: true)
                            ->placeholder('Auto-generated from title if blank'),
                        Forms\Components\Textarea::make("summary.{$locale}")
                            ->label('Summary')
                            ->maxLength(500)
                            ->rows(2),
                        Forms\Components\RichEditor::make("description.{$locale}")
                            ->label('Description')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
                ]),
                Schemas\Components\Tabs\Tab::make('Settings')->schema([
                    Schemas\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255)
                            ->placeholder('Where were these photos taken?'),
                        Forms\Components\DatePicker::make('taken_at')
                            ->label('Date Taken'),
                    ]),
                    Schemas\Components\Grid::make(3)->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured'),
                        Forms\Components\Toggle::make('is_visible')
                            ->label('Visible')
                            ->default(true),
                    ]),
                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                ]),
                Schemas\Components\Tabs\Tab::make('Cover Image')->schema([
                    Forms\Components\SpatieMediaLibraryFileUpload::make('cover')
                        ->collection('cover')
                        ->image()
                        ->imageEditor(),
                ]),
                Schemas\Components\Tabs\Tab::make('Photos')->schema([
                    Forms\Components\SpatieMediaLibraryFileUpload::make('photos')
                        ->collection('photos')
                        ->multiple()
                        ->reorderable()
                        ->image()
                        ->helperText('Upload multiple photos. You can drag to reorder.'),
                ]),
                Schemas\Components\Tabs\Tab::make('SEO')->schema([
                    TranslatableSchema::seoTabs(),
                    Schemas\Components\Section::make('JSON-LD')->schema([
                        TranslatableSchema::jsonLdTabs(),
                    ])->collapsed(),
                    Forms\Components\FileUpload::make('og_image')
                        ->image()
                        ->directory('seo'),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->circular()
                    ->label(''),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TranslationCompleteness::column('title'),
                Tables\Columns\TextColumn::make('photo_count')
                    ->label('Photos')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'warning',
                        'published' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                Tables\Columns\TextColumn::make('taken_at')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
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
            'index' => Pages\ListAlbums::route('/'),
            'create' => Pages\CreateAlbum::route('/create'),
            'edit' => Pages\EditAlbum::route('/{record}/edit'),
        ];
    }
}
