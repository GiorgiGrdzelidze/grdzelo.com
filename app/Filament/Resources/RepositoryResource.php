<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatableSchema;
use App\Filament\Concerns\TranslationCompleteness;
use App\Filament\Resources\RepositoryResource\Pages;
use App\Models\Repository;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class RepositoryResource extends Resource
{
    protected static ?string $model = Repository::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-code-bracket';

    protected static string|\UnitEnum|null $navigationGroup = 'Portfolio';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Tabs::make('Repository')->tabs([
                Schemas\Components\Tabs\Tab::make('Translations')->schema([
                    TranslatableSchema::tabs(fn (string $locale, bool $isDefault) => [
                        Forms\Components\TextInput::make("name.{$locale}")
                            ->label('Name')
                            ->required($isDefault)
                            ->maxLength(255),
                        Forms\Components\TextInput::make("slug.{$locale}")
                            ->label('Slug')
                            ->maxLength(255)
                            ->unique(table: 'repositories', column: "slug->{$locale}", ignoreRecord: true)
                            ->placeholder('Auto-generated from name if blank'),
                        Forms\Components\Textarea::make("summary.{$locale}")
                            ->label('Summary')
                            ->maxLength(500)
                            ->rows(2),
                        Forms\Components\RichEditor::make("description.{$locale}")
                            ->label('Description')
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('repositories'),
                    ])->columnSpanFull(),
                ]),
                Schemas\Components\Tabs\Tab::make('Settings')->schema([
                    Forms\Components\TextInput::make('url')
                        ->label('Repository URL')
                        ->required()
                        ->url()
                        ->maxLength(255)
                        ->placeholder('https://github.com/username/repo'),
                    Schemas\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('owner')
                            ->maxLength(255)
                            ->placeholder('GitHub username or org'),
                        Forms\Components\TextInput::make('language')
                            ->maxLength(100)
                            ->placeholder('Primary language'),
                    ]),
                    Forms\Components\TagsInput::make('technologies')
                        ->placeholder('Add technology'),
                    Schemas\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('stars')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\TextInput::make('forks')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ]),
                    Schemas\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'archived' => 'Archived',
                                'experimental' => 'Experimental',
                                'deprecated' => 'Deprecated',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\TextInput::make('demo_url')
                            ->label('Demo/Live URL')
                            ->url()
                            ->maxLength(255),
                    ]),
                    Schemas\Components\Grid::make(3)->schema([
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured'),
                        Forms\Components\Toggle::make('is_visible')
                            ->label('Visible')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ]),
                    Forms\Components\Select::make('project_id')
                        ->relationship('project', 'title')
                        ->searchable()
                        ->preload()
                        ->label('Related Project'),
                ]),
                Schemas\Components\Tabs\Tab::make('Media')->schema([
                    Forms\Components\FileUpload::make('thumbnail')
                        ->image()
                        ->directory('repositories')
                        ->imageEditor(),
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
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->circular()
                    ->label(''),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TranslationCompleteness::column('name'),
                Tables\Columns\TextColumn::make('language')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('stars')
                    ->sortable()
                    ->icon('heroicon-o-star'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'experimental',
                        'gray' => 'archived',
                        'danger' => 'deprecated',
                    ]),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                Tables\Columns\IconColumn::make('is_visible')
                    ->boolean()
                    ->label('Visible'),
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
                        'active' => 'Active',
                        'archived' => 'Archived',
                        'experimental' => 'Experimental',
                        'deprecated' => 'Deprecated',
                    ]),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured'),
                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label('Visible'),
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
            'index' => Pages\ListRepositories::route('/'),
            'create' => Pages\CreateRepository::route('/create'),
            'edit' => Pages\EditRepository::route('/{record}/edit'),
        ];
    }
}
