<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\TranslatableMediaAlt;
use App\Filament\Resources\CertificationResource\Pages;
use App\Models\Certification;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class CertificationResource extends Resource
{
    use TranslatableMediaAlt;

    protected static ?string $model = Certification::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';

    protected static string|\UnitEnum|null $navigationGroup = 'Profile';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\TextInput::make('issuing_organization')->required()->maxLength(255),
            Schemas\Components\Grid::make(3)->schema([
                Forms\Components\DatePicker::make('issue_date')->required(),
                Forms\Components\DatePicker::make('expiry_date')
                    ->visible(fn (Schemas\Components\Utilities\Get $get) => ! $get('no_expiry')),
                Forms\Components\Toggle::make('no_expiry')->label('No Expiry')->reactive(),
            ]),
            Schemas\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('credential_id')->maxLength(255),
                Forms\Components\TextInput::make('credential_url')->url()->maxLength(255),
            ]),
            Forms\Components\Textarea::make('description')->rows(3),
            Forms\Components\Select::make('skills')
                ->relationship('skills', 'name')
                ->multiple()
                ->preload()
                ->searchable(),
            Forms\Components\SpatieMediaLibraryFileUpload::make('badge')
                ->collection('badge')
                ->image(),
            static::mediaAltField('badge', 'Badge alt'),
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
                Tables\Columns\ImageColumn::make('badge_image')->circular()->label(''),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('issuing_organization')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('issue_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('expiry_date')->date()->sortable()
                    ->placeholder('No Expiry'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'Active' => 'success',
                        'Expired' => 'danger',
                        'No Expiry' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_visible')->boolean()->label('Visible'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible')->label('Visible'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
                Tables\Filters\TernaryFilter::make('no_expiry')->label('No Expiry'),
            ])
            ->recordActions([Actions\EditAction::make()])
            ->toolbarActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertifications::route('/'),
            'create' => Pages\CreateCertification::route('/create'),
            'edit' => Pages\EditCertification::route('/{record}/edit'),
        ];
    }
}
