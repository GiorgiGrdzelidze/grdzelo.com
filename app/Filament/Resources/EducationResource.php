<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EducationResource\Pages;
use App\Models\Education;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EducationResource extends Resource
{
    protected static ?string $model = Education::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string | \UnitEnum | null $navigationGroup = 'Profile';

    protected static ?int $navigationSort = 3;

    protected static ?string $pluralModelLabel = 'Education';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('institution')->required()->maxLength(255),
            Schemas\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('degree')->maxLength(255),
                Forms\Components\TextInput::make('field_of_study')->maxLength(255),
            ]),
            Forms\Components\TextInput::make('location')->maxLength(255),
            Schemas\Components\Grid::make(3)->schema([
                Forms\Components\DatePicker::make('start_date')->required(),
                Forms\Components\DatePicker::make('end_date'),
                Forms\Components\Toggle::make('is_current')->label('Currently Studying'),
            ]),
            Forms\Components\Textarea::make('description')->rows(4),
            Forms\Components\TagsInput::make('achievements')->placeholder('Add achievement'),
            Forms\Components\FileUpload::make('logo')->image()->directory('education'),
            Schemas\Components\Grid::make(3)->schema([
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                Forms\Components\Toggle::make('is_featured')->label('Featured'),
                Forms\Components\Toggle::make('is_visible')->label('Visible')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')->circular()->label(''),
                Tables\Columns\TextColumn::make('institution')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('degree')->searchable(),
                Tables\Columns\TextColumn::make('field_of_study')->toggleable(),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
                Tables\Columns\IconColumn::make('is_current')->boolean()->label('Current'),
                Tables\Columns\IconColumn::make('is_visible')->boolean()->label('Visible'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible')->label('Visible'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
            ])
            ->actions([Actions\EditAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEducation::route('/'),
            'create' => Pages\CreateEducation::route('/create'),
            'edit' => Pages\EditEducation::route('/{record}/edit'),
        ];
    }
}
