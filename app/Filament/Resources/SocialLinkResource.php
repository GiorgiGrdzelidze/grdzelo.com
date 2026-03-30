<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialLinkResource\Pages;
use App\Models\SocialLink;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SocialLinkResource extends Resource
{
    protected static ?string $model = SocialLink::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static string|\UnitEnum|null $navigationGroup = 'Social';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('platform')
                ->options([
                    'github' => 'GitHub',
                    'linkedin' => 'LinkedIn',
                    'twitter' => 'Twitter / X',
                    'instagram' => 'Instagram',
                    'youtube' => 'YouTube',
                    'dribbble' => 'Dribbble',
                    'behance' => 'Behance',
                    'medium' => 'Medium',
                    'devto' => 'Dev.to',
                    'stackoverflow' => 'Stack Overflow',
                    'telegram' => 'Telegram',
                    'discord' => 'Discord',
                    'facebook' => 'Facebook',
                    'website' => 'Website',
                    'other' => 'Other',
                ])
                ->required()
                ->searchable(),
            Forms\Components\TextInput::make('label')->maxLength(255),
            Forms\Components\TextInput::make('url')->required()->url()->maxLength(255),
            Forms\Components\TextInput::make('username')->maxLength(255),
            Forms\Components\TextInput::make('icon')->maxLength(255)->helperText('Override icon name'),
            Schemas\Components\Grid::make(3)->schema([
                Forms\Components\Toggle::make('is_visible')->label('Visible')->default(true),
                Forms\Components\Toggle::make('is_highlighted')->label('Highlighted'),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('platform')->badge()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('label')->searchable(),
                Tables\Columns\TextColumn::make('url')->limit(40)->toggleable(),
                Tables\Columns\TextColumn::make('username'),
                Tables\Columns\IconColumn::make('is_visible')->boolean()->label('Visible'),
                Tables\Columns\IconColumn::make('is_highlighted')->boolean()->label('Highlighted'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([Actions\EditAction::make()])
            ->toolbarActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSocialLinks::route('/'),
            'create' => Pages\CreateSocialLink::route('/create'),
            'edit' => Pages\EditSocialLink::route('/{record}/edit'),
        ];
    }
}
