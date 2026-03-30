<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialFeedItemResource\Pages;
use App\Models\SocialFeedItem;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SocialFeedItemResource extends Resource
{
    protected static ?string $model = SocialFeedItem::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-photo';

    protected static string | \UnitEnum | null $navigationGroup = 'Social';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Feed Items';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('platform')
                ->options([
                    'instagram' => 'Instagram',
                    'twitter' => 'Twitter / X',
                    'linkedin' => 'LinkedIn',
                    'other' => 'Other',
                ])
                ->searchable(),
            Forms\Components\FileUpload::make('media_url')
                ->label('Media')
                ->image()
                ->directory('social-feed'),
            Forms\Components\FileUpload::make('thumbnail_url')
                ->label('Thumbnail')
                ->image()
                ->directory('social-feed/thumbs'),
            Forms\Components\Textarea::make('caption')->rows(3),
            Forms\Components\TextInput::make('external_url')->url()->maxLength(255)->label('External Link'),
            Forms\Components\DateTimePicker::make('posted_at')->label('Posted At'),
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
                Tables\Columns\ImageColumn::make('media_url')->label('Media')->square(),
                Tables\Columns\TextColumn::make('platform')->badge(),
                Tables\Columns\TextColumn::make('caption')->limit(50),
                Tables\Columns\TextColumn::make('posted_at')->dateTime()->sortable(),
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\IconColumn::make('is_visible')->boolean()->label('Visible'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([Actions\EditAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSocialFeedItems::route('/'),
            'create' => Pages\CreateSocialFeedItem::route('/create'),
            'edit' => Pages\EditSocialFeedItem::route('/{record}/edit'),
        ];
    }
}
