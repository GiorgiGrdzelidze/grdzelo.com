<?php

namespace App\Filament\Resources;

use App\Enums\Currency;
use App\Enums\PayFrequency;
use App\Filament\Resources\SalaryRecordResource\Pages;
use App\Models\SalaryRecord;
use App\Settings\FinanceSettings;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SalaryRecordResource extends Resource
{
    protected static ?string $model = SalaryRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        $defaultTax = app(FinanceSettings::class)->default_tax_percentage;

        return $schema->schema([
            Forms\Components\TextInput::make('employer')->required()->maxLength(255),
            Forms\Components\Select::make('income_source_id')
                ->relationship('incomeSource', 'title')
                ->searchable()
                ->preload()
                ->helperText('Optionally link to an income source'),
            Schemas\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('gross_amount')
                    ->numeric()->required()
                    ->reactive()
                    ->afterStateUpdated(function (Schemas\Components\Utilities\Get $get, Schemas\Components\Utilities\Set $set) {
                        $gross = (float) $get('gross_amount');
                        $taxPct = (float) $get('tax_percentage');
                        $calc = SalaryRecord::calculateTax($gross, $taxPct);
                        $set('tax_amount', $calc['tax_amount']);
                        $set('net_amount', $calc['net_amount']);
                    }),
                Forms\Components\Select::make('currency')
                    ->options(Currency::options())
                    ->default('GEL')
                    ->required(),
            ]),
            Schemas\Components\Grid::make(3)->schema([
                Forms\Components\TextInput::make('tax_percentage')
                    ->numeric()->suffix('%')->default($defaultTax)
                    ->reactive()
                    ->afterStateUpdated(function (Schemas\Components\Utilities\Get $get, Schemas\Components\Utilities\Set $set) {
                        $gross = (float) $get('gross_amount');
                        $taxPct = (float) $get('tax_percentage');
                        $calc = SalaryRecord::calculateTax($gross, $taxPct);
                        $set('tax_amount', $calc['tax_amount']);
                        $set('net_amount', $calc['net_amount']);
                    }),
                Forms\Components\TextInput::make('tax_amount')->numeric()->readOnly(),
                Forms\Components\TextInput::make('net_amount')->numeric()->readOnly(),
            ]),
            Schemas\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('pay_frequency')
                    ->options(PayFrequency::options())
                    ->default('monthly')
                    ->required(),
                Forms\Components\TextInput::make('payment_day')
                    ->numeric()->minValue(1)->maxValue(31)
                    ->label('Payment Day of Month'),
            ]),
            Schemas\Components\Grid::make(3)->schema([
                Forms\Components\DatePicker::make('start_date')->required(),
                Forms\Components\DatePicker::make('end_date'),
                Forms\Components\Toggle::make('is_active')->label('Active')->default(true),
            ]),
            Forms\Components\Textarea::make('notes')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employer')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('gross_amount')
                    ->formatStateUsing(fn (SalaryRecord $record) => $record->currency->symbol().number_format((float) $record->gross_amount, 2))
                    ->sortable()
                    ->label('Gross'),
                Tables\Columns\TextColumn::make('tax_percentage')->suffix('%')->sortable()->label('Tax %'),
                Tables\Columns\TextColumn::make('net_amount')
                    ->formatStateUsing(fn (SalaryRecord $record) => $record->currency->symbol().number_format((float) $record->net_amount, 2))
                    ->sortable()
                    ->label('Net'),
                Tables\Columns\TextColumn::make('pay_frequency')
                    ->badge()
                    ->formatStateUsing(fn (PayFrequency $state) => $state->label()),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([Actions\EditAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalaryRecords::route('/'),
            'create' => Pages\CreateSalaryRecord::route('/create'),
            'edit' => Pages\EditSalaryRecord::route('/{record}/edit'),
        ];
    }
}
