<?php

namespace App\Filament\Resources;

use App\Enums\BillingInterval;
use App\Enums\Currency;
use App\Enums\IncomeType;
use App\Filament\Resources\IncomeSourceResource\Pages;
use App\Filament\Resources\IncomeSourceResource\RelationManagers\EntriesRelationManager;
use App\Models\IncomeSource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class IncomeSourceResource extends Resource
{
    protected static ?string $model = IncomeSource::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\Select::make('type')
                ->options(IncomeType::options())
                ->default('freelance')
                ->required(),
            Schemas\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('amount')->numeric()->required(),
                Forms\Components\Select::make('currency')
                    ->options(Currency::options())
                    ->default('GEL')
                    ->required(),
            ]),
            Schemas\Components\Grid::make(2)->schema([
                Forms\Components\Toggle::make('is_recurring')->label('Recurring')->reactive(),
                Forms\Components\Select::make('interval')
                    ->options(BillingInterval::options())
                    ->visible(fn (Schemas\Components\Utilities\Get $get) => $get('is_recurring')),
            ]),
            Schemas\Components\Grid::make(3)->schema([
                Forms\Components\DatePicker::make('start_date'),
                Forms\Components\DatePicker::make('end_date'),
                Forms\Components\TextInput::make('expected_day')
                    ->numeric()->minValue(1)->maxValue(31)
                    ->label('Expected Day of Month'),
            ]),
            Forms\Components\Textarea::make('notes')->rows(3),
            Forms\Components\Toggle::make('is_active')->label('Active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (IncomeType $state) => $state->label()),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn (IncomeSource $record) => $record->currency->symbol().number_format((float) $record->amount, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('interval')->toggleable(),
                Tables\Columns\IconColumn::make('is_recurring')->boolean()->label('Recurring'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options(IncomeType::options()),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([Actions\EditAction::make()])
            ->toolbarActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            EntriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncomeSources::route('/'),
            'create' => Pages\CreateIncomeSource::route('/create'),
            'edit' => Pages\EditIncomeSource::route('/{record}/edit'),
        ];
    }
}
