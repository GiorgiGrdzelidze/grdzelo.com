<?php

namespace App\Filament\Resources;

use App\Enums\Currency;
use App\Filament\Resources\ExchangeRateResource\Pages;
use App\Models\ExchangeRate;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ExchangeRateResource extends Resource
{
    protected static ?string $model = ExchangeRate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('from_currency')
                    ->options(Currency::options())
                    ->required(),
                Forms\Components\Select::make('to_currency')
                    ->options(Currency::options())
                    ->required(),
            ]),
            Schemas\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('rate')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->step(0.00000001),
                Forms\Components\DatePicker::make('rate_date')
                    ->required()
                    ->default(now()),
            ]),
            Forms\Components\TextInput::make('source')
                ->default('manual')
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('from_currency')->sortable(),
                Tables\Columns\TextColumn::make('to_currency')->sortable(),
                Tables\Columns\TextColumn::make('rate')->sortable()
                    ->formatStateUsing(fn (string $state) => number_format((float) $state, 6)),
                Tables\Columns\TextColumn::make('rate_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('source')->badge()->toggleable(),
            ])
            ->defaultSort('rate_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('from_currency')
                    ->options(Currency::options()),
                Tables\Filters\SelectFilter::make('to_currency')
                    ->options(Currency::options()),
            ])
            ->recordActions([Actions\EditAction::make()])
            ->toolbarActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExchangeRates::route('/'),
            'create' => Pages\CreateExchangeRate::route('/create'),
            'edit' => Pages\EditExchangeRate::route('/{record}/edit'),
        ];
    }
}
