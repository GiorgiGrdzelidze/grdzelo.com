<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

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
                Schemas\Components\Tabs\Tab::make('General')->schema([
                    Forms\Components\TextInput::make('title')->required()->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Schemas\Components\Utilities\Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                    Forms\Components\TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                    Forms\Components\Textarea::make('summary')->maxLength(500)->rows(3),
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
                Schemas\Components\Tabs\Tab::make('Content')->schema([
                    Forms\Components\RichEditor::make('description')->columnSpanFull(),
                ]),
                Schemas\Components\Tabs\Tab::make('SEO')->schema([
                    Forms\Components\TextInput::make('meta_title')->maxLength(255),
                    Forms\Components\Textarea::make('meta_description')->maxLength(500)->rows(3),
                    Forms\Components\TextInput::make('canonical_url')->url()->maxLength(255),
                    Forms\Components\TextInput::make('og_title')->maxLength(255),
                    Forms\Components\Textarea::make('og_description')->maxLength(500)->rows(2),
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
                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([Actions\EditAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
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
