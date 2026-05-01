<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatableSchema;
use App\Filament\Concerns\TranslationCompleteness;
use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static string|\UnitEnum|null $navigationGroup = 'Portfolio';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Tabs::make('Service')->tabs([
                Schemas\Components\Tabs\Tab::make('Translations')->schema([
                    TranslatableSchema::tabs(fn (string $locale, bool $isDefault) => [
                        Forms\Components\TextInput::make("title.{$locale}")
                            ->label('Title')
                            ->required($isDefault)
                            ->maxLength(255),
                        Forms\Components\TextInput::make("slug.{$locale}")
                            ->label('Slug')
                            ->maxLength(255)
                            ->unique(table: 'services', column: "slug->{$locale}", ignoreRecord: true)
                            ->placeholder('Auto-generated from title if blank'),
                        Forms\Components\Textarea::make("summary.{$locale}")
                            ->label('Summary')
                            ->maxLength(500)
                            ->rows(3),
                        Forms\Components\RichEditor::make("description.{$locale}")
                            ->label('Description')
                            ->columnSpanFull(),
                    ])->columnSpanFull(),
                ]),
                Schemas\Components\Tabs\Tab::make('Settings')->schema([
                    Forms\Components\TextInput::make('icon')->maxLength(255)->helperText('Heroicon name or SVG'),
                    Schemas\Components\Grid::make(2)->schema([
                        Forms\Components\Toggle::make('is_featured')->label('Featured'),
                        Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                    ]),
                    Schemas\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('cta_text')->label('CTA Text')->maxLength(255),
                        Forms\Components\TextInput::make('cta_url')->label('CTA URL')->maxLength(255),
                    ]),
                ]),
                Schemas\Components\Tabs\Tab::make('SEO')->schema([
                    TranslatableSchema::seoTabs(),
                    Forms\Components\FileUpload::make('og_image')->image()->directory('seo'),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                TranslationCompleteness::column('title'),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([Actions\EditAction::make()])
            ->toolbarActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
