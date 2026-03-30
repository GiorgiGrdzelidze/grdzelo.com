<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SkillResource\Pages;
use App\Models\Skill;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SkillResource extends Resource
{
    protected static ?string $model = Skill::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string|\UnitEnum|null $navigationGroup = 'Profile';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Schemas\Components\Utilities\Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
            Forms\Components\TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('category')->maxLength(255)
                ->helperText('e.g. Backend, Frontend, DevOps, Database, Tools'),
            Schemas\Components\Grid::make(3)->schema([
                Forms\Components\TextInput::make('proficiency_label')->maxLength(255)->placeholder('e.g. Expert, Advanced'),
                Forms\Components\TextInput::make('proficiency_score')->numeric()->minValue(0)->maxValue(100)->suffix('%'),
                Forms\Components\TextInput::make('years_experience')->numeric()->minValue(0)->suffix('years'),
            ]),
            Forms\Components\TextInput::make('icon')->maxLength(255)->helperText('Icon name or URL'),
            Schemas\Components\Grid::make(3)->schema([
                Forms\Components\Toggle::make('is_featured')->label('Featured'),
                Forms\Components\Toggle::make('is_visible')->label('Visible')->default(true),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')->sortable()->badge(),
                Tables\Columns\TextColumn::make('proficiency_label'),
                Tables\Columns\TextColumn::make('years_experience')->suffix(' yrs')->sortable(),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\IconColumn::make('is_visible')->boolean()->label('Visible'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(fn () => Skill::query()->distinct()->pluck('category', 'category')->filter()->toArray()),
            ])
            ->actions([Actions\EditAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSkills::route('/'),
            'create' => Pages\CreateSkill::route('/create'),
            'edit' => Pages\EditSkill::route('/{record}/edit'),
        ];
    }
}
