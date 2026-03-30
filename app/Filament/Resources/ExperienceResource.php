<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExperienceResource\Pages;
use App\Models\Experience;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExperienceResource extends Resource
{
    protected static ?string $model = Experience::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clock';

    protected static string | \UnitEnum | null $navigationGroup = 'Profile';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('company')->required()->maxLength(255),
            Forms\Components\TextInput::make('role')->required()->maxLength(255),
            Forms\Components\Select::make('type')
                ->options([
                    'job' => 'Job',
                    'freelance' => 'Freelance',
                    'project' => 'Project',
                    'milestone' => 'Milestone',
                    'speaking' => 'Speaking',
                    'award' => 'Award',
                ])
                ->default('job')
                ->required(),
            Schemas\Components\Grid::make(3)->schema([
                Forms\Components\DatePicker::make('start_date')->required(),
                Forms\Components\DatePicker::make('end_date'),
                Forms\Components\Toggle::make('is_current')->label('Currently Here'),
            ]),
            Forms\Components\Textarea::make('summary')->rows(4),
            Forms\Components\TagsInput::make('achievements')->placeholder('Add achievement'),
            Forms\Components\TagsInput::make('technologies')->placeholder('Add technology'),
            Schemas\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('website_url')->url()->maxLength(255),
                Forms\Components\FileUpload::make('logo')->image()->directory('experiences'),
            ]),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')->circular()->label(''),
                Tables\Columns\TextColumn::make('company')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('role')->searchable(),
                Tables\Columns\BadgeColumn::make('type'),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
                Tables\Columns\IconColumn::make('is_current')->boolean()->label('Current'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'job' => 'Job',
                        'freelance' => 'Freelance',
                        'project' => 'Project',
                        'milestone' => 'Milestone',
                        'speaking' => 'Speaking',
                        'award' => 'Award',
                    ]),
            ])
            ->actions([Actions\EditAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExperiences::route('/'),
            'create' => Pages\CreateExperience::route('/create'),
            'edit' => Pages\EditExperience::route('/{record}/edit'),
        ];
    }
}
