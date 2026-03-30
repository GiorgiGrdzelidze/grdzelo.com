<?php

namespace App\Filament\Widgets;

use App\Enums\Currency;
use App\Enums\IncomeType;
use App\Models\IncomeEntry;
use App\Models\IncomeSource;
use App\Settings\FinanceSettings;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class IncomeByTypeWidget extends ChartWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'half';

    public function getHeading(): string
    {
        return 'Income by Type (This Month)';
    }

    protected function getData(): array
    {
        $baseCurrency = app(FinanceSettings::class)->base_currency;
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $sources = IncomeSource::with(['entries' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('date', [$startOfMonth, $endOfMonth])->where('is_received', true);
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

        $filtered = $byType->filter(fn ($amount) => $amount > 0);

        if ($filtered->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'data' => [1],
                        'backgroundColor' => ['#e5e7eb'],
                    ],
                ],
                'labels' => ['No data'],
            ];
        }

        $colors = [
            'salary' => '#10b981',
            'rent' => '#3b82f6',
            'freelance' => '#8b5cf6',
            'project' => '#f59e0b',
            'passive' => '#06b6d4',
            'other' => '#6b7280',
        ];

        return [
            'datasets' => [
                [
                    'data' => $filtered->values()->toArray(),
                    'backgroundColor' => $filtered->keys()->map(fn ($type) => $colors[$type] ?? '#6b7280')->toArray(),
                ],
            ],
            'labels' => $filtered->keys()->map(fn ($type) => IncomeType::tryFrom($type)?->label() ?? ucfirst($type))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => true,
        ];
    }
}
