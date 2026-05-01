<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatableSchema;
use App\Filament\Concerns\TranslationCompleteness;
use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Portfolio';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Tabs::make('Project')->tabs([
                Schemas\Components\Tabs\Tab::make('Translations')->schema([
                    TranslatableSchema::tabs(fn (string $locale, bool $isDefault) => [
                        Forms\Components\TextInput::make("title.{$locale}")
                            ->label('Title')
                            ->required($isDefault)
                            ->maxLength(255),
                        Forms\Components\TextInput::make("slug.{$locale}")
                            ->label('Slug')
                            ->maxLength(255)
                            ->unique(table: 'projects', column: "slug->{$locale}", ignoreRecord: true)
                            ->placeholder('Auto-generated from title if blank'),
                        Forms\Components\Textarea::make("summary.{$locale}")
                            ->label('Summary')
                            ->maxLength(500)
                            ->rows(3),
                        Forms\Components\RichEditor::make("description.{$locale}")
                            ->label('Full Description')
                            ->columnSpanFull()
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('projects'),
                        Forms\Components\Textarea::make("challenge.{$locale}")
                            ->label('The Challenge')
                            ->rows(4),
                        Forms\Components\Textarea::make("solution.{$locale}")
                            ->label('The Solution')
                            ->rows(4),
                        Forms\Components\Textarea::make("process.{$locale}")
                            ->label('The Process')
                            ->rows(4),
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
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Featured Project'),
                    ]),
                    Schemas\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('role')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('client_type')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('industry')
                            ->maxLength(255),
                    ]),
                    Schemas\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('year')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2099),
                        Forms\Components\DatePicker::make('date_start'),
                        Forms\Components\DatePicker::make('date_end'),
                    ]),
                    Schemas\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('live_url')
                            ->label('Live URL')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('repo_url')
                            ->label('Repository URL')
                            ->url()
                            ->maxLength(255),
                    ]),
                    Schemas\Components\Grid::make(2)->schema([
                        Forms\Components\Toggle::make('is_visible')
                            ->label('Publicly Visible')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ]),
                    Forms\Components\TagsInput::make('tech_stack')
                        ->label('Tech Stack')
                        ->placeholder('Add technology'),
                    Forms\Components\Repeater::make('metrics')
                        ->schema([
                            Forms\Components\TextInput::make('label')
                                ->required(),
                            Forms\Components\TextInput::make('value')
                                ->required(),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->addActionLabel('Add Metric'),
                    Forms\Components\Repeater::make('case_study_blocks')
                        ->label('Case Study Blocks')
                        ->schema([
                            Forms\Components\TextInput::make('heading'),
                            Forms\Components\RichEditor::make('content'),
                            Forms\Components\FileUpload::make('image')
                                ->image()
                                ->directory('projects/case-study'),
                        ])
                        ->defaultItems(0)
                        ->addActionLabel('Add Block')
                        ->collapsible(),
                ]),
                Schemas\Components\Tabs\Tab::make('Media')->schema([
                    Forms\Components\FileUpload::make('cover_image')
                        ->image()
                        ->directory('projects')
                        ->imageEditor(),
                    Forms\Components\FileUpload::make('logo')
                        ->image()
                        ->directory('projects/logos'),
                    Forms\Components\FileUpload::make('gallery')
                        ->image()
                        ->multiple()
                        ->directory('projects/gallery')
                        ->reorderable(),
                ]),
                Schemas\Components\Tabs\Tab::make('Relations')->schema([
                    Forms\Components\Select::make('skills')
                        ->relationship('skills', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
                    Forms\Components\Select::make('testimonials')
                        ->relationship('testimonials', 'author_name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
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
                    TranslatableSchema::seoTabs(),
                    Schemas\Components\Section::make('Indexing')->schema([
                        Schemas\Components\Grid::make(2)->schema([
                            Forms\Components\Toggle::make('noindex'),
                            Forms\Components\Toggle::make('nofollow'),
                        ]),
                        Forms\Components\TextInput::make('breadcrumb_title')
                            ->maxLength(255),
                    ]),
                    Schemas\Components\Section::make('Open Graph media')->schema([
                        Forms\Components\FileUpload::make('og_image')->image()->directory('seo'),
                    ]),
                    Schemas\Components\Section::make('Twitter media')->schema([
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
                Tables\Columns\ImageColumn::make('cover_image')
                    ->circular()
                    ->label(''),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TranslationCompleteness::column('title'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'draft',
                        'success' => 'published',
                    ]),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),
                Tables\Columns\TextColumn::make('year')
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
