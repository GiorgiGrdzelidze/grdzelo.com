<?php

namespace App\Filament\Resources;

use App\Enums\BillingInterval;
use App\Enums\Currency;
use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\Select::make('expense_category_id')
                ->relationship('category', 'title')
                ->searchable()
                ->preload()
                ->createOptionForm([
                    Forms\Components\TextInput::make('title')->required(),
                    Forms\Components\TextInput::make('slug')->required(),
                ]),
            Schemas\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('amount')->numeric()->required(),
                Forms\Components\Select::make('currency')
                    ->options(Currency::options())
                    ->default('GEL')
                    ->required(),
            ]),
            Forms\Components\DatePicker::make('date')->required()->default(now()),
            Schemas\Components\Grid::make(2)->schema([
                Forms\Components\Toggle::make('is_recurring')->label('Recurring'),
                Forms\Components\Select::make('interval')
                    ->options(BillingInterval::options())
                    ->visible(fn (Schemas\Components\Utilities\Get $get) => $get('is_recurring')),
            ]),
            Forms\Components\Textarea::make('note')->rows(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.title')->sortable()->badge(),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn (Expense $record) => $record->currency->symbol().number_format((float) $record->amount, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\IconColumn::make('is_recurring')->boolean()->label('Recurring'),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('expense_category_id')
                    ->relationship('category', 'title')
                    ->label('Category'),
                Tables\Filters\TernaryFilter::make('is_recurring')->label('Recurring'),
            ])
            ->recordActions([Actions\EditAction::make()])
            ->toolbarActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
