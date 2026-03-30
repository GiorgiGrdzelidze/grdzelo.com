<?php

namespace App\Filament\Pages;

use App\Enums\Currency;
use App\Enums\IncomeType;
use App\Models\ExchangeRate;
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

    public ?string $period = 'this_month';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public function updatedPeriod(): void
    {
        if ($this->period !== 'custom') {
            $this->dateFrom = null;
            $this->dateTo = null;
        }
    }

    public function getPeriodOptions(): array
    {
        return [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            'this_week' => 'This Week',
            'last_week' => 'Last Week',
            'this_month' => 'This Month',
            'last_month' => 'Last Month',
            'this_quarter' => 'This Quarter',
            'last_quarter' => 'Last Quarter',
            'this_year' => 'This Year',
            'last_year' => 'Last Year',
            'all_time' => 'All Time',
            'custom' => 'Custom Range',
        ];
    }

    protected function getDateRange(): array
    {
        $now = Carbon::now();

        return match ($this->period) {
            'today' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
                'label' => 'Today',
            ],
            'yesterday' => [
                'start' => $now->copy()->subDay()->startOfDay(),
                'end' => $now->copy()->subDay()->endOfDay(),
                'label' => 'Yesterday',
            ],
            'this_week' => [
                'start' => $now->copy()->startOfWeek(),
                'end' => $now->copy()->endOfWeek(),
                'label' => 'This Week',
            ],
            'last_week' => [
                'start' => $now->copy()->subWeek()->startOfWeek(),
                'end' => $now->copy()->subWeek()->endOfWeek(),
                'label' => 'Last Week',
            ],
            'this_month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
                'label' => 'This Month',
            ],
            'last_month' => [
                'start' => $now->copy()->subMonth()->startOfMonth(),
                'end' => $now->copy()->subMonth()->endOfMonth(),
                'label' => 'Last Month',
            ],
            'this_quarter' => [
                'start' => $now->copy()->startOfQuarter(),
                'end' => $now->copy()->endOfQuarter(),
                'label' => 'This Quarter',
            ],
            'last_quarter' => [
                'start' => $now->copy()->subQuarter()->startOfQuarter(),
                'end' => $now->copy()->subQuarter()->endOfQuarter(),
                'label' => 'Last Quarter',
            ],
            'this_year' => [
                'start' => $now->copy()->startOfYear(),
                'end' => $now->copy()->endOfYear(),
                'label' => 'This Year',
            ],
            'last_year' => [
                'start' => $now->copy()->subYear()->startOfYear(),
                'end' => $now->copy()->subYear()->endOfYear(),
                'label' => 'Last Year',
            ],
            'all_time' => [
                'start' => Carbon::create(2000, 1, 1),
                'end' => $now->copy()->endOfDay(),
                'label' => 'All Time',
            ],
            'custom' => [
                'start' => $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay() : $now->copy()->startOfMonth(),
                'end' => $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay() : $now->copy()->endOfDay(),
                'label' => 'Custom Range',
            ],
            default => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth(),
                'label' => 'This Month',
            ],
        };
    }

    public function getViewData(): array
    {
        $settings = app(FinanceSettings::class);
        $baseCurrency = $settings->statistics_currency ?? $settings->base_currency ?? 'GEL';
        $baseSymbol = Currency::tryFrom($baseCurrency)?->symbol() ?? $baseCurrency;
        $baseCurrencyEnum = Currency::tryFrom($baseCurrency);

        $dateRange = $this->getDateRange();
        $start = $dateRange['start'];
        $end = $dateRange['end'];
        $periodLabel = $dateRange['label'];

        $now = Carbon::now();
        $startOfYear = $now->copy()->startOfYear();
        $endOfYear = $now->copy()->endOfYear();

        return [
            'baseCurrency' => $baseCurrency,
            'baseSymbol' => $baseSymbol,
            'baseCurrencyEnum' => $baseCurrencyEnum,
            'periodLabel' => $periodLabel,
            'periodStart' => $start->format('M j, Y'),
            'periodEnd' => $end->format('M j, Y'),
            'periodStats' => $this->getPeriodStats($baseCurrency, $start, $end),
            'yearlyStats' => $this->getYearlyStats($baseCurrency, $startOfYear, $endOfYear),
            'incomeByType' => $this->getIncomeByType($baseCurrency, $start, $end),
            'subscriptionStats' => $this->getSubscriptionStats($baseCurrency),
            'salaryStats' => $this->getSalaryStats($baseCurrency),
            'expectedVsReceived' => $this->getExpectedVsReceived($baseCurrency, $start, $end),
            'recentIncome' => $this->getRecentIncome(10, $start, $end),
            'recentExpenses' => $this->getRecentExpenses(null, $baseCurrency, $start, $end),
            'upcomingRenewals' => $this->getUpcomingRenewals(7),
            'expensesByCategory' => $this->getExpensesByCategory($baseCurrency, $start, $end),
            'monthlyTrend' => $this->getMonthlyTrend($baseCurrency, 12),
        ];
    }

    protected function getPeriodStats(string $baseCurrency, Carbon $start, Carbon $end): array
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
        $allSubscriptions = Subscription::all();

        $monthlyTotal = 0.0;
        $byCurrency = [];
        $totalSpentSinceStart = 0.0;

        foreach ($active as $sub) {
            $currency = $sub->currency->value;
            $monthly = $sub->monthly_amount;

            if (! isset($byCurrency[$currency])) {
                $byCurrency[$currency] = 0.0;
            }
            $byCurrency[$currency] += $monthly;

            $monthlyTotal += $this->convertToBase($monthly, $currency, $baseCurrency);
        }

        $monthlyByCurrencyConverted = [];
        foreach ($byCurrency as $currency => $amount) {
            $monthlyByCurrencyConverted[$currency] = [
                'original' => $amount,
                'converted' => $this->convertToBase($amount, $currency, $baseCurrency),
            ];
        }

        foreach ($allSubscriptions as $sub) {
            if (! $sub->start_date) {
                continue;
            }

            $currency = $sub->currency->value;
            $monthsSinceStart = $sub->start_date->diffInMonths(now());
            $totalForSub = $sub->monthly_amount * max(1, $monthsSinceStart);

            $totalSpentSinceStart += $this->convertToBase($totalForSub, $currency, $baseCurrency);
        }

        $upcomingCount = Subscription::upcomingRenewal(7)->count();

        return [
            'active_count' => $active->count(),
            'paused_count' => $paused,
            'cancelled_count' => $cancelled,
            'monthly_total' => $monthlyTotal,
            'monthly_by_currency' => $byCurrency,
            'monthly_by_currency_converted' => $monthlyByCurrencyConverted,
            'upcoming_renewals' => $upcomingCount,
            'total_spent_since_start' => $totalSpentSinceStart,
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

    protected function getRecentIncome(int $limit, ?Carbon $start = null, ?Carbon $end = null): Collection
    {
        $query = IncomeEntry::with('source')->orderByDesc('date');

        if ($start && $end) {
            $query->whereBetween('date', [$start, $end]);
        }

        return $query->limit($limit)
            ->get()
            ->map(fn (IncomeEntry $entry) => [
                'id' => $entry->id,
                'date' => $entry->date->format('M j, Y'),
                'source' => $entry->source?->title ?? 'Unknown',
                'amount' => $entry->currency->format((float) $entry->amount),
                'currency' => $entry->currency->value,
                'is_received' => $entry->is_received,
            ]);
    }

    protected function getRecentExpenses(?int $limit = null, ?string $baseCurrency = null, ?Carbon $start = null, ?Carbon $end = null): Collection
    {
        $query = Expense::with('category')->orderByDesc('date');

        if ($start && $end) {
            $query->whereBetween('date', [$start, $end]);
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get()
            ->map(function (Expense $expense) use ($baseCurrency) {
                $amount = (float) $expense->amount;
                $currency = $expense->currency->value;
                $baseCurrencyEnum = Currency::tryFrom($baseCurrency);

                if ($baseCurrency && $currency !== $baseCurrency && $baseCurrencyEnum) {
                    $converted = $this->convertToBase($amount, $currency, $baseCurrency);
                    $displayAmount = $baseCurrencyEnum->format($converted);
                } else {
                    $displayAmount = $expense->currency->format($amount);
                }

                return [
                    'id' => $expense->id,
                    'date' => $expense->date->format('M j, Y'),
                    'title' => $expense->title,
                    'category' => $expense->category?->title ?? 'Uncategorized',
                    'amount' => $displayAmount,
                    'original_amount' => $expense->currency->format($amount),
                    'currency' => $currency,
                ];
            });
    }

    protected function getUpcomingRenewals(int $days): Collection
    {
        return Subscription::upcomingRenewal($days)
            ->orderBy('next_billing_date')
            ->get()
            ->map(fn (Subscription $sub) => [
                'id' => $sub->id,
                'title' => $sub->title,
                'amount' => $sub->currency->format((float) $sub->amount),
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

        $nonConverted = (clone $query)
            ->where('currency', '!=', $baseCurrency)
            ->whereNull('base_amount')
            ->get();

        $additionalConverted = 0.0;
        foreach ($nonConverted as $item) {
            $additionalConverted += $this->convertToBase(
                (float) $item->amount,
                $item->currency->value ?? $item->currency,
                $baseCurrency
            );
        }

        return (float) $baseSum + (float) $convertedSum + $additionalConverted;
    }

    protected function convertToBase(float $amount, string $fromCurrency, string $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $result = ExchangeRate::convert($amount, $fromCurrency, $toCurrency);

        if ($result) {
            return $result['amount'];
        }

        $reverseResult = ExchangeRate::convert(1, $toCurrency, $fromCurrency);
        if ($reverseResult && $reverseResult['rate'] > 0) {
            return $amount / $reverseResult['rate'];
        }

        return $amount;
    }
}
