<?php

namespace App\Filament\Widgets;

use App\Enums\Currency;
use App\Models\Expense;
use App\Models\IncomeEntry;
use App\Models\SalaryRecord;
use App\Models\Subscription;
use App\Settings\FinanceSettings;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class FinanceOverviewWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $baseCurrency = app(FinanceSettings::class)->base_currency;
        $symbol = Currency::tryFrom($baseCurrency)?->symbol() ?? $baseCurrency;

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfYear = Carbon::now()->startOfYear();

        $monthlyIncome = $this->sumWithConversion(
            IncomeEntry::whereBetween('date', [$startOfMonth, $endOfMonth])->where('is_received', true),
            $baseCurrency
        );

        $monthlyExpenses = $this->sumWithConversion(
            Expense::whereBetween('date', [$startOfMonth, $endOfMonth]),
            $baseCurrency
        );

        $netIncome = $monthlyIncome - $monthlyExpenses;

        $ytdIncome = $this->sumWithConversion(
            IncomeEntry::whereBetween('date', [$startOfYear, $endOfMonth])->where('is_received', true),
            $baseCurrency
        );

        $ytdExpenses = $this->sumWithConversion(
            Expense::whereBetween('date', [$startOfYear, $endOfMonth]),
            $baseCurrency
        );

        $activeSubscriptions = Subscription::active()->count();
        $upcomingRenewals = Subscription::upcomingRenewal(7)->count();

        $activeSalaryTax = SalaryRecord::active()
            ->where('currency', $baseCurrency)
            ->sum('tax_amount');

        return [
            Stat::make('Monthly Income', $symbol . number_format($monthlyIncome, 2))
                ->description('Received this month')
                ->icon('heroicon-o-arrow-trending-up')
                ->color('success'),

            Stat::make('Monthly Expenses', $symbol . number_format($monthlyExpenses, 2))
                ->description('Spent this month')
                ->icon('heroicon-o-arrow-trending-down')
                ->color('danger'),

            Stat::make('Monthly Net', $symbol . number_format($netIncome, 2))
                ->description($netIncome >= 0 ? 'Positive cash flow' : 'Negative cash flow')
                ->icon('heroicon-o-banknotes')
                ->color($netIncome >= 0 ? 'success' : 'danger'),

            Stat::make('YTD Net', $symbol . number_format($ytdIncome - $ytdExpenses, 2))
                ->description('Year-to-date')
                ->icon('heroicon-o-calendar')
                ->color(($ytdIncome - $ytdExpenses) >= 0 ? 'success' : 'danger'),

            Stat::make('Subscriptions', (string) $activeSubscriptions)
                ->description($upcomingRenewals > 0 ? $upcomingRenewals . ' renewing soon' : 'Active')
                ->icon('heroicon-o-arrow-path')
                ->color($upcomingRenewals > 0 ? 'warning' : 'info'),

            Stat::make('Monthly Tax', $symbol . number_format($activeSalaryTax, 2))
                ->description('From active salaries')
                ->icon('heroicon-o-receipt-percent')
                ->color('gray'),
        ];
    }

    protected function sumWithConversion($query, string $baseCurrency): float
    {
        $baseSum = (clone $query)
            ->where('currency', $baseCurrency)
            ->sum('amount');

        $convertedSum = (clone $query)
            ->where('currency', '!=', $baseCurrency)
            ->whereNotNull('base_amount')
            ->sum('base_amount');

        return (float) $baseSum + (float) $convertedSum;
    }
}
