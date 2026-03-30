<?php

namespace App\Filament\Pages;

use App\Enums\Currency;
use App\Settings\FinanceSettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;
use Filament\Schemas;
use Filament\Schemas\Schema;

class ManageFinanceSettings extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Finance Settings';

    protected static string $settings = FinanceSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Currency')->schema([
                Forms\Components\Select::make('base_currency')
                    ->options(Currency::options())
                    ->required()
                    ->helperText('Default currency for new entries'),
                Forms\Components\Select::make('statistics_currency')
                    ->options(Currency::options())
                    ->default('GEL')
                    ->helperText('Currency used for statistics page (all amounts will be converted to this)'),
                Forms\Components\TagsInput::make('supported_currencies')
                    ->placeholder('Add currency code (e.g. USD)')
                    ->helperText('Currencies available for selection in finance forms'),
            ]),
            Schemas\Components\Section::make('Tax')->schema([
                Forms\Components\TextInput::make('default_tax_percentage')
                    ->numeric()->suffix('%')
                    ->helperText('Default income tax rate for salary calculations'),
                Forms\Components\TextInput::make('fiscal_year_start_month')
                    ->numeric()->minValue(1)->maxValue(12)
                    ->helperText('Month number (1-12) when your fiscal year starts'),
            ]),
            Schemas\Components\Section::make('Reminders')->schema([
                Forms\Components\Toggle::make('reminders_enabled')
                    ->label('Enable Subscription Reminders'),
                Forms\Components\TextInput::make('default_reminder_days_before')
                    ->numeric()->minValue(0)->maxValue(30)
                    ->label('Default Days Before Billing')
                    ->helperText('Default reminder offset for new subscriptions'),
            ]),
        ]);
    }
}
