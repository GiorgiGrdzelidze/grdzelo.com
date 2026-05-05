<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatableSchema;
use App\Filament\Concerns\TranslationCompleteness;
use App\Filament\Resources\HobbyResource\Pages;
use App\Models\Hobby;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class HobbyResource extends Resource
{
    protected static ?string $model = Hobby::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static string|\UnitEnum|null $navigationGroup = 'Profile';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            TranslatableSchema::tabs(fn (string $locale, bool $isDefault) => [
                Forms\Components\TextInput::make("title.{$locale}")
                    ->label('Title')
                    ->required($isDefault)
                    ->maxLength(255),
                Forms\Components\TextInput::make("slug.{$locale}")
                    ->label('Slug')
                    ->maxLength(255)
                    ->unique(table: 'hobbies', column: "slug->{$locale}", ignoreRecord: true)
                    ->placeholder('Auto-generated from title if blank'),
                Forms\Components\Textarea::make("summary.{$locale}")
                    ->label('Summary')
                    ->maxLength(500)
                    ->rows(3),
                Forms\Components\RichEditor::make("description.{$locale}")
                    ->label('Description')
                    ->columnSpanFull(),
            ])->columnSpanFull(),
            Forms\Components\TextInput::make('icon')->maxLength(255),
            Forms\Components\SpatieMediaLibraryFileUpload::make('cover')
                ->collection('cover')
                ->image(),
            Forms\Components\SpatieMediaLibraryFileUpload::make('gallery')
                ->collection('gallery')
                ->image()
                ->multiple()
                ->reorderable(),
            Schemas\Components\Grid::make(3)->schema([
                Forms\Components\Toggle::make('is_featured')->label('Featured'),
                Forms\Components\Toggle::make('is_visible')->label('Visible')->default(true),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ]),
            Schemas\Components\Section::make('SEO')->schema([
                TranslatableSchema::seoTabs(),
                Schemas\Components\Section::make('JSON-LD')->schema([
                    TranslatableSchema::jsonLdTabs(),
                ])->collapsed(),
                Forms\Components\FileUpload::make('og_image')->image()->directory('seo'),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->circular()->label(''),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                TranslationCompleteness::column('title'),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\IconColumn::make('is_visible')->boolean()->label('Visible'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([Actions\EditAction::make()])
            ->toolbarActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHobbies::route('/'),
            'create' => Pages\CreateHobby::route('/create'),
            'edit' => Pages\EditHobby::route('/{record}/edit'),
        ];
    }
}
