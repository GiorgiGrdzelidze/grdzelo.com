<?php

namespace App\Filament\Pages;

use App\Enums\Currency;
use App\Enums\IncomeType;
use App\Models\Expense;
use App\Models\IncomeEntry;
use App\Models\IncomeSource;
use App\Models\SalaryRecord;
use App\Models\Subscription;
use App\Settings\FinanceSettings;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class FinanceStatistics extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Statistics';

    protected static ?string $title = 'Finance Statistics';

    protected string $view = 'filament.pages.finance-statistics';

    public function getViewData(): array
    {
        $settings = app(FinanceSettings::class);
        $baseCurrency = $settings->base_currency;
        $baseSymbol = Currency::tryFrom($baseCurrency)?->symbol() ?? $baseCurrency;

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();

        return [
            'baseCurrency' => $baseCurrency,
            'baseSymbol' => $baseSymbol,
            'monthlyStats' => $this->getMonthlyStats($baseCurrency, $startOfMonth, $endOfMonth),
            'yearlyStats' => $this->getYearlyStats($baseCurrency, $startOfYear, $endOfYear),
            'incomeByType' => $this->getIncomeByType($baseCurrency, $startOfYear, $endOfYear),
            'incomeByTypeMTD' => $this->getIncomeByType($baseCurrency, $startOfMonth, $endOfMonth),
            'subscriptionStats' => $this->getSubscriptionStats($baseCurrency),
            'salaryStats' => $this->getSalaryStats($baseCurrency),
            'expectedVsReceived' => $this->getExpectedVsReceived($baseCurrency, $startOfMonth, $endOfMonth),
            'recentIncome' => $this->getRecentIncome(5),
            'recentExpenses' => $this->getRecentExpenses(5),
            'upcomingRenewals' => $this->getUpcomingRenewals(7),
            'expensesByCategory' => $this->getExpensesByCategory($baseCurrency, $startOfMonth, $endOfMonth),
            'monthlyTrend' => $this->getMonthlyTrend($baseCurrency, 6),
        ];
    }

    protected function getMonthlyStats(string $baseCurrency, Carbon $start, Carbon $end): array
    {
        $income = $this->sumWithConversion(
            IncomeEntry::whereBetween('date', [$start, $end])->where('is_received', true),
            $baseCurrency
        );

        $expenses = $this->sumWithConversion(
            Expense::whereBetween('date', [$start, $end]),
            $baseCurrency
        );

        return [
            'income' => $income,
            'expenses' => $expenses,
            'net' => $income - $expenses,
        ];
    }

    protected function getYearlyStats(string $baseCurrency, Carbon $start, Carbon $end): array
    {
        $income = $this->sumWithConversion(
            IncomeEntry::whereBetween('date', [$start, $end])->where('is_received', true),
            $baseCurrency
        );

        $expenses = $this->sumWithConversion(
            Expense::whereBetween('date', [$start, $end]),
            $baseCurrency
        );

        return [
            'income' => $income,
            'expenses' => $expenses,
            'net' => $income - $expenses,
        ];
    }

    protected function getIncomeByType(string $baseCurrency, Carbon $start, Carbon $end): Collection
    {
        $sources = IncomeSource::with(['entries' => function ($query) use ($start, $end) {
            $query->whereBetween('date', [$start, $end])->where('is_received', true);
        }])->get();

        $byType = collect(IncomeType::cases())->mapWithKeys(fn (IncomeType $type) => [$type->value => 0.0]);

        foreach ($sources as $source) {
            $type = $source->type?->value ?? 'other';
            foreach ($source->entries as $entry) {
                if ($entry->currency->value === $baseCurrency) {
                    $byType[$type] += (float) $entry->amount;
                } elseif ($entry->base_amount) {
                    $byType[$type] += (float) $entry->base_amount;
                }
            }
        }

        return $byType->map(fn ($amount, $type) => [
            'type' => IncomeType::tryFrom($type)?->label() ?? ucfirst($type),
            'amount' => $amount,
        ])->filter(fn ($item) => $item['amount'] > 0)->values();
    }

    protected function getSubscriptionStats(string $baseCurrency): array
    {
        $active = Subscription::active()->get();
        $paused = Subscription::paused()->count();
        $cancelled = Subscription::cancelled()->count();

        $monthlyTotal = 0.0;
        $byCurrency = [];

        foreach ($active as $sub) {
            $currency = $sub->currency->value;
            $monthly = $sub->monthly_amount;

            if (! isset($byCurrency[$currency])) {
                $byCurrency[$currency] = 0.0;
            }
            $byCurrency[$currency] += $monthly;

            if ($currency === $baseCurrency) {
                $monthlyTotal += $monthly;
            }
        }

        $upcomingCount = Subscription::upcomingRenewal(7)->count();

        return [
            'active_count' => $active->count(),
            'paused_count' => $paused,
            'cancelled_count' => $cancelled,
            'monthly_total' => $monthlyTotal,
            'monthly_by_currency' => $byCurrency,
            'upcoming_renewals' => $upcomingCount,
        ];
    }

    protected function getSalaryStats(string $baseCurrency): array
    {
        $activeSalaries = SalaryRecord::active()->get();

        $totalGross = 0.0;
        $totalTax = 0.0;
        $totalNet = 0.0;
        $byCurrency = [];

        foreach ($activeSalaries as $salary) {
            $currency = $salary->currency->value;

            if (! isset($byCurrency[$currency])) {
                $byCurrency[$currency] = ['gross' => 0.0, 'tax' => 0.0, 'net' => 0.0];
            }

            $byCurrency[$currency]['gross'] += (float) $salary->gross_amount;
            $byCurrency[$currency]['tax'] += (float) $salary->tax_amount;
            $byCurrency[$currency]['net'] += (float) $salary->net_amount;

            if ($currency === $baseCurrency) {
                $totalGross += (float) $salary->gross_amount;
                $totalTax += (float) $salary->tax_amount;
                $totalNet += (float) $salary->net_amount;
            }
        }

        return [
            'count' => $activeSalaries->count(),
            'total_gross' => $totalGross,
            'total_tax' => $totalTax,
            'total_net' => $totalNet,
            'by_currency' => $byCurrency,
        ];
    }

    protected function getExpectedVsReceived(string $baseCurrency, Carbon $start, Carbon $end): array
    {
        $expected = $this->sumWithConversion(
            IncomeEntry::whereBetween('date', [$start, $end]),
            $baseCurrency
        );

        $received = $this->sumWithConversion(
            IncomeEntry::whereBetween('date', [$start, $end])->where('is_received', true),
            $baseCurrency
        );

        return [
            'expected' => $expected,
            'received' => $received,
            'outstanding' => $expected - $received,
            'percentage' => $expected > 0 ? round(($received / $expected) * 100, 1) : 100,
        ];
    }

    protected function getRecentIncome(int $limit): Collection
    {
        return IncomeEntry::with('source')
            ->orderByDesc('date')
            ->limit($limit)
            ->get()
            ->map(fn (IncomeEntry $entry) => [
                'id' => $entry->id,
                'date' => $entry->date->format('M j, Y'),
                'source' => $entry->source?->title ?? 'Unknown',
                'amount' => $entry->currency->symbol() . number_format((float) $entry->amount, 2),
                'currency' => $entry->currency->value,
                'is_received' => $entry->is_received,
            ]);
    }

    protected function getRecentExpenses(int $limit): Collection
    {
        return Expense::with('category')
            ->orderByDesc('date')
            ->limit($limit)
            ->get()
            ->map(fn (Expense $expense) => [
                'id' => $expense->id,
                'date' => $expense->date->format('M j, Y'),
                'title' => $expense->title,
                'category' => $expense->category?->title ?? 'Uncategorized',
                'amount' => $expense->currency->symbol() . number_format((float) $expense->amount, 2),
                'currency' => $expense->currency->value,
            ]);
    }

    protected function getUpcomingRenewals(int $days): Collection
    {
        return Subscription::upcomingRenewal($days)
            ->orderBy('next_billing_date')
            ->get()
            ->map(fn (Subscription $sub) => [
                'id' => $sub->id,
                'title' => $sub->title,
                'amount' => $sub->currency->symbol() . number_format((float) $sub->amount, 2),
                'next_billing_date' => $sub->next_billing_date->format('M j, Y'),
                'days_until' => $sub->next_billing_date->diffInDays(now()),
            ]);
    }

    protected function getExpensesByCategory(string $baseCurrency, Carbon $start, Carbon $end): Collection
    {
        return Expense::with('category')
            ->whereBetween('date', [$start, $end])
            ->get()
            ->groupBy(fn (Expense $e) => $e->category?->title ?? 'Uncategorized')
            ->map(function (Collection $expenses, string $category) use ($baseCurrency) {
                $total = 0.0;
                foreach ($expenses as $expense) {
                    if ($expense->currency->value === $baseCurrency) {
                        $total += (float) $expense->amount;
                    } elseif ($expense->base_amount) {
                        $total += (float) $expense->base_amount;
                    }
                }

                return [
                    'category' => $category,
                    'amount' => $total,
                    'count' => $expenses->count(),
                ];
            })
            ->sortByDesc('amount')
            ->values();
    }

    protected function getMonthlyTrend(string $baseCurrency, int $months): Collection
    {
        $trend = collect();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $income = $this->sumWithConversion(
                IncomeEntry::whereBetween('date', [$start, $end])->where('is_received', true),
                $baseCurrency
            );

            $expenses = $this->sumWithConversion(
                Expense::whereBetween('date', [$start, $end]),
                $baseCurrency
            );

            $trend->push([
                'month' => $date->format('M Y'),
                'income' => $income,
                'expenses' => $expenses,
                'net' => $income - $expenses,
            ]);
        }

        return $trend;
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
