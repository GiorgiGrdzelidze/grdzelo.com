<?php

namespace App\Filament\Widgets;

use App\Enums\Currency;
use App\Models\Expense;
use App\Models\IncomeEntry;
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

        $monthlyIncome = IncomeEntry::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('currency', $baseCurrency)
            ->sum('amount');

        $monthlyIncomeConverted = IncomeEntry::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('currency', '!=', $baseCurrency)
            ->whereNotNull('base_amount')
            ->sum('base_amount');

        $totalIncome = $monthlyIncome + $monthlyIncomeConverted;

        $monthlyExpenses = Expense::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('currency', $baseCurrency)
            ->sum('amount');

        $monthlyExpensesConverted = Expense::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->where('currency', '!=', $baseCurrency)
            ->whereNotNull('base_amount')
            ->sum('base_amount');

        $totalExpenses = $monthlyExpenses + $monthlyExpensesConverted;
        $netIncome = $totalIncome - $totalExpenses;

        $activeSubscriptions = Subscription::active()->count();
        $monthlySubCost = Subscription::active()
            ->get()
            ->sum(fn (Subscription $s) => $s->monthly_amount);

        return [
            Stat::make('Monthly Income', $symbol . number_format($totalIncome, 2))
                ->description('In ' . $baseCurrency)
                ->icon('heroicon-o-arrow-trending-up')
                ->color('success'),
            Stat::make('Monthly Expenses', $symbol . number_format($totalExpenses, 2))
                ->description('In ' . $baseCurrency)
                ->icon('heroicon-o-arrow-trending-down')
                ->color('danger'),
            Stat::make('Net Income', $symbol . number_format($netIncome, 2))
                ->icon('heroicon-o-banknotes')
                ->color($netIncome >= 0 ? 'success' : 'danger'),
            Stat::make('Active Subscriptions', (string) $activeSubscriptions)
                ->description($symbol . number_format($monthlySubCost, 2) . '/mo')
                ->icon('heroicon-o-arrow-path')
                ->color('info'),
        ];
    }
}
