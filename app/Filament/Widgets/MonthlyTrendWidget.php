<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\IncomeEntry;
use App\Settings\FinanceSettings;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MonthlyTrendWidget extends ChartWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'half';

    public function getHeading(): string
    {
        return 'Income vs Expenses (6 Months)';
    }

    protected function getData(): array
    {
        $baseCurrency = app(FinanceSettings::class)->base_currency;
        $months = 6;

        $labels = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $labels[] = $date->format('M');

            $income = $this->sumWithConversion(
                IncomeEntry::whereBetween('date', [$start, $end])->where('is_received', true),
                $baseCurrency
            );

            $expenses = $this->sumWithConversion(
                Expense::whereBetween('date', [$start, $end]),
                $baseCurrency
            );

            $incomeData[] = round($income, 2);
            $expenseData[] = round($expenses, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Income',
                    'data' => $incomeData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Expenses',
                    'data' => $expenseData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
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
