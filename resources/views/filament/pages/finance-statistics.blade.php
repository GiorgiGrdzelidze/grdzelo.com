<x-filament-panels::page>
    <style>
        .fi-stats-grid { display: grid; gap: 1rem; }
        @media (min-width: 768px) { .fi-stats-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .fi-stats-grid { grid-template-columns: repeat(4, 1fr); } }
        .fi-stats-grid-2 { display: grid; gap: 1.5rem; }
        @media (min-width: 1024px) { .fi-stats-grid-2 { grid-template-columns: repeat(2, 1fr); } }
        .fi-stats-grid-3 { display: grid; gap: 1rem; }
        @media (min-width: 768px) { .fi-stats-grid-3 { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1024px) { .fi-stats-grid-3 { grid-template-columns: repeat(3, 1fr); } }
        .fi-stat-card { display: flex; align-items: center; gap: 1rem; }
        .fi-stat-icon { padding: 0.75rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; }
        .fi-stat-icon svg { width: 1.5rem; height: 1.5rem; }
        .fi-stat-label { font-size: 0.875rem; font-weight: 500; color: rgb(107, 114, 128); }
        .fi-stat-value { font-size: 1.5rem; font-weight: 700; }
        .fi-stat-value-lg { font-size: 1.875rem; font-weight: 700; }
        .fi-stat-desc { font-size: 0.75rem; color: rgb(107, 114, 128); }
        .fi-section-heading { display: flex; align-items: center; gap: 0.5rem; }
        .fi-section-heading svg { width: 1.25rem; height: 1.25rem; }
        .fi-text-success { color: rgb(16, 185, 129); }
        .fi-text-danger { color: rgb(239, 68, 68); }
        .fi-text-warning { color: rgb(245, 158, 11); }
        .fi-text-info { color: rgb(14, 165, 233); }
        .fi-text-muted { color: rgb(107, 114, 128); }
        .fi-bg-success { background: rgba(16, 185, 129, 0.1); }
        .fi-bg-danger { background: rgba(239, 68, 68, 0.1); }
        .fi-bg-warning { background: rgba(245, 158, 11, 0.1); }
        .fi-bg-info { background: rgba(14, 165, 233, 0.1); }
        .fi-card { border: 1px solid rgb(229, 231, 235); border-radius: 0.5rem; padding: 1rem; }
        .dark .fi-card { border-color: rgb(55, 65, 81); }
        .fi-card-warning { border-color: rgb(253, 230, 138); background: rgba(245, 158, 11, 0.05); }
        .dark .fi-card-warning { border-color: rgb(120, 53, 15); background: rgba(245, 158, 11, 0.1); }
        .fi-flex-between { display: flex; justify-content: space-between; align-items: center; }
        .fi-text-center { text-align: center; }
        .fi-text-right { text-align: right; }
        .fi-font-medium { font-weight: 500; }
        .fi-font-semibold { font-weight: 600; }
        .fi-font-bold { font-weight: 700; }
        .fi-text-sm { font-size: 0.875rem; }
        .fi-text-xs { font-size: 0.75rem; }
        .fi-text-2xl { font-size: 1.5rem; }
        .fi-space-y-3 > * + * { margin-top: 0.75rem; }
        .fi-space-y-4 > * + * { margin-top: 1rem; }
        .fi-space-y-6 > * + * { margin-top: 1.5rem; }
        .fi-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
        .fi-progress-bar { height: 0.5rem; width: 100%; border-radius: 9999px; background: rgb(229, 231, 235); }
        .dark .fi-progress-bar { background: rgb(55, 65, 81); }
        .fi-progress-fill { height: 0.5rem; border-radius: 9999px; background: rgb(16, 185, 129); }
        .fi-badge { display: inline-flex; align-items: center; padding: 0.125rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
        .fi-badge-primary { background: rgba(99, 102, 241, 0.1); color: rgb(99, 102, 241); }
        .fi-badge-warning { background: rgba(245, 158, 11, 0.1); color: rgb(245, 158, 11); }
        .fi-table { width: 100%; font-size: 0.875rem; }
        .fi-table th { padding: 0.5rem 1rem; text-align: left; font-weight: 500; color: rgb(107, 114, 128); border-bottom: 1px solid rgb(229, 231, 235); }
        .fi-table td { padding: 0.75rem 1rem; border-bottom: 1px solid rgb(243, 244, 246); }
        .dark .fi-table th { border-color: rgb(55, 65, 81); }
        .dark .fi-table td { border-color: rgb(31, 41, 55); }
        .fi-alert { padding: 0.75rem; border-radius: 0.5rem; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; }
        .fi-alert svg { width: 1rem; height: 1rem; flex-shrink: 0; }
        .fi-alert-warning { background: rgba(245, 158, 11, 0.1); color: rgb(180, 83, 9); }
        .dark .fi-alert-warning { color: rgb(252, 211, 77); }
        .fi-border-t { border-top: 1px solid rgb(229, 231, 235); padding-top: 1rem; margin-top: 1rem; }
        .dark .fi-border-t { border-color: rgb(55, 65, 81); }
    </style>

    <div class="fi-space-y-6">
        {{-- Top Summary Cards --}}
        <div class="fi-stats-grid">
            <x-filament::section>
                <div class="fi-stat-card">
                    <div class="fi-stat-icon fi-bg-success">
                        <x-filament::icon icon="heroicon-o-arrow-trending-up" class="fi-text-success" />
                    </div>
                    <div>
                        <p class="fi-stat-label">Monthly Income</p>
                        <p class="fi-stat-value fi-text-success">{{ $baseCurrencyEnum?->format($monthlyStats['income']) ?? number_format($monthlyStats['income'], 2) }}</p>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="fi-stat-card">
                    <div class="fi-stat-icon fi-bg-danger">
                        <x-filament::icon icon="heroicon-o-arrow-trending-down" class="fi-text-danger" />
                    </div>
                    <div>
                        <p class="fi-stat-label">Monthly Expenses</p>
                        <p class="fi-stat-value fi-text-danger">{{ $baseCurrencyEnum?->format($monthlyStats['expenses']) ?? number_format($monthlyStats['expenses'], 2) }}</p>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="fi-stat-card">
                    <div class="fi-stat-icon {{ $monthlyStats['net'] >= 0 ? 'fi-bg-success' : 'fi-bg-danger' }}">
                        <x-filament::icon icon="heroicon-o-banknotes" class="{{ $monthlyStats['net'] >= 0 ? 'fi-text-success' : 'fi-text-danger' }}" />
                    </div>
                    <div>
                        <p class="fi-stat-label">Monthly Net</p>
                        <p class="fi-stat-value {{ $monthlyStats['net'] >= 0 ? 'fi-text-success' : 'fi-text-danger' }}">{{ $baseCurrencyEnum?->format($monthlyStats['net']) ?? number_format($monthlyStats['net'], 2) }}</p>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="fi-stat-card">
                    <div class="fi-stat-icon fi-bg-info">
                        <x-filament::icon icon="heroicon-o-arrow-path" class="fi-text-info" />
                    </div>
                    <div>
                        <p class="fi-stat-label">Active Subscriptions</p>
                        <p class="fi-stat-value">{{ $subscriptionStats['active_count'] }}</p>
                        <p class="fi-stat-desc">{{ $baseCurrencyEnum?->format($subscriptionStats['monthly_total']) ?? number_format($subscriptionStats['monthly_total'], 2) }}/mo</p>
                    </div>
                </div>
            </x-filament::section>
        </div>

        {{-- Year-to-Date Summary --}}
        <x-filament::section>
            <x-slot name="heading">
                <span class="fi-section-heading">
                    <x-filament::icon icon="heroicon-o-calendar" />
                    Year-to-Date Summary ({{ $baseCurrency }})
                </span>
            </x-slot>

            <div class="fi-grid-3">
                <div class="fi-text-center">
                    <p class="fi-stat-label">Total Income</p>
                    <p class="fi-stat-value-lg fi-text-success">{{ $baseCurrencyEnum?->format($yearlyStats['income']) }}</p>
                </div>
                <div class="fi-text-center">
                    <p class="fi-stat-label">Total Expenses</p>
                    <p class="fi-stat-value-lg fi-text-danger">{{ $baseCurrencyEnum?->format($yearlyStats['expenses']) }}</p>
                </div>
                <div class="fi-text-center">
                    <p class="fi-stat-label">Net Income</p>
                    <p class="fi-stat-value-lg {{ $yearlyStats['net'] >= 0 ? 'fi-text-success' : 'fi-text-danger' }}">{{ $baseCurrencyEnum?->format($yearlyStats['net']) }}</p>
                </div>
            </div>
        </x-filament::section>

        <div class="fi-stats-grid-2">
            {{-- Income by Type (YTD) --}}
            <x-filament::section>
                <x-slot name="heading">
                    <span class="fi-section-heading">
                        <x-filament::icon icon="heroicon-o-chart-pie" />
                        Income by Type (YTD)
                    </span>
                </x-slot>

                @if($incomeByType->isNotEmpty())
                    <div class="fi-space-y-3">
                        @foreach($incomeByType as $item)
                            <div class="fi-flex-between">
                                <span class="fi-badge fi-badge-primary">{{ $item['type'] }}</span>
                                <span class="fi-font-semibold">{{ $baseCurrencyEnum?->format($item['amount']) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="fi-text-muted fi-text-sm">No income recorded this year.</p>
                @endif
            </x-filament::section>

            {{-- Expected vs Received --}}
            <x-filament::section>
                <x-slot name="heading">
                    <span class="fi-section-heading">
                        <x-filament::icon icon="heroicon-o-check-badge" />
                        Expected vs Received (This Month)
                    </span>
                </x-slot>

                <div class="fi-space-y-4">
                    <div class="fi-flex-between fi-text-sm">
                        <span class="fi-text-muted">Expected</span>
                        <span class="fi-font-medium">{{ $baseCurrencyEnum?->format($expectedVsReceived['expected']) }}</span>
                    </div>
                    <div class="fi-flex-between fi-text-sm">
                        <span class="fi-text-muted">Received</span>
                        <span class="fi-font-medium fi-text-success">{{ $baseCurrencyEnum?->format($expectedVsReceived['received']) }}</span>
                    </div>
                    <div class="fi-flex-between fi-text-sm">
                        <span class="fi-text-muted">Outstanding</span>
                        <span class="fi-font-medium {{ $expectedVsReceived['outstanding'] > 0 ? 'fi-text-warning' : 'fi-text-success' }}">{{ $baseCurrencyEnum?->format($expectedVsReceived['outstanding']) }}</span>
                    </div>
                    <div>
                        <div class="fi-flex-between fi-text-xs fi-text-muted" style="margin-bottom: 0.25rem;">
                            <span>Collection Rate</span>
                            <span>{{ $expectedVsReceived['percentage'] }}%</span>
                        </div>
                        <div class="fi-progress-bar">
                            <div class="fi-progress-fill" style="width: {{ min($expectedVsReceived['percentage'], 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <div class="fi-stats-grid-2">
            {{-- Salary Overview --}}
            <x-filament::section>
                <x-slot name="heading">
                    <span class="fi-section-heading">
                        <x-filament::icon icon="heroicon-o-building-office" />
                        Active Salary Records
                    </span>
                </x-slot>

                @if($salaryStats['count'] > 0)
                    <div class="fi-space-y-4">
                        @foreach($salaryStats['by_currency'] as $currency => $amounts)
                            <div class="fi-card">
                                <p class="fi-text-sm fi-font-medium fi-text-muted" style="margin-bottom: 0.5rem;">{{ $currency }}</p>
                                <div class="fi-grid-3 fi-text-center">
                                    <div>
                                        <p class="fi-text-xs fi-text-muted">Gross</p>
                                        <p class="fi-font-semibold">{{ \App\Enums\Currency::tryFrom($currency)?->format($amounts['gross']) }}</p>
                                    </div>
                                    <div>
                                        <p class="fi-text-xs fi-text-muted">Tax</p>
                                        <p class="fi-font-semibold fi-text-danger">{{ \App\Enums\Currency::tryFrom($currency)?->format($amounts['tax']) }}</p>
                                    </div>
                                    <div>
                                        <p class="fi-text-xs fi-text-muted">Net</p>
                                        <p class="fi-font-semibold fi-text-success">{{ \App\Enums\Currency::tryFrom($currency)?->format($amounts['net']) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="fi-text-muted fi-text-sm">No active salary records.</p>
                @endif
            </x-filament::section>

            {{-- Subscription Breakdown --}}
            <x-filament::section>
                <x-slot name="heading">
                    <span class="fi-section-heading">
                        <x-filament::icon icon="heroicon-o-arrow-path" />
                        Subscription Breakdown
                    </span>
                </x-slot>

                <div class="fi-space-y-4">
                    <div class="fi-grid-3 fi-text-center">
                        <div>
                            <p class="fi-text-2xl fi-font-bold fi-text-success">{{ $subscriptionStats['active_count'] }}</p>
                            <p class="fi-text-xs fi-text-muted">Active</p>
                        </div>
                        <div>
                            <p class="fi-text-2xl fi-font-bold fi-text-warning">{{ $subscriptionStats['paused_count'] }}</p>
                            <p class="fi-text-xs fi-text-muted">Paused</p>
                        </div>
                        <div>
                            <p class="fi-text-2xl fi-font-bold fi-text-muted">{{ $subscriptionStats['cancelled_count'] }}</p>
                            <p class="fi-text-xs fi-text-muted">Cancelled</p>
                        </div>
                    </div>

                    @if(isset($subscriptionStats['total_spent_since_start']) && $subscriptionStats['total_spent_since_start'] > 0)
                        <div class="fi-border-t">
                            <div class="fi-flex-between fi-text-sm">
                                <span class="fi-text-muted">Total Spent (All Time)</span>
                                <span class="fi-font-bold fi-text-danger">{{ $baseCurrencyEnum?->format($subscriptionStats['total_spent_since_start']) }}</span>
                            </div>
                        </div>
                    @endif

                    @if(count($subscriptionStats['monthly_by_currency_converted'] ?? []) > 0)
                        <div class="fi-border-t">
                            <p class="fi-text-sm fi-font-medium fi-text-muted" style="margin-bottom: 0.5rem;">Monthly Cost (in {{ $baseCurrency }})</p>
                            @foreach($subscriptionStats['monthly_by_currency_converted'] as $currency => $data)
                                <div class="fi-flex-between fi-text-sm">
                                    <span>{{ $currency }}</span>
                                    <span class="fi-font-medium">{{ $baseCurrencyEnum?->format($data['converted']) }}/mo</span>
                                </div>
                            @endforeach
                            <div class="fi-flex-between fi-text-sm fi-font-bold" style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid rgba(107, 114, 128, 0.2);">
                                <span>Total</span>
                                <span class="fi-text-danger">{{ $baseCurrencyEnum?->format($subscriptionStats['monthly_total']) ?? number_format($subscriptionStats['monthly_total'], 2) }}/mo</span>
                            </div>
                        </div>
                    @endif

                    @if($subscriptionStats['upcoming_renewals'] > 0)
                        <div class="fi-alert fi-alert-warning">
                            <x-filament::icon icon="heroicon-o-exclamation-triangle" />
                            <span>{{ $subscriptionStats['upcoming_renewals'] }} subscription(s) renewing in the next 7 days</span>
                        </div>
                    @endif
                </div>
            </x-filament::section>
        </div>

        {{-- Expenses by Category --}}
        <x-filament::section>
            <x-slot name="heading">
                <span class="fi-section-heading">
                    <x-filament::icon icon="heroicon-o-tag" />
                    Expenses by Category (This Month)
                </span>
            </x-slot>

            @if($expensesByCategory->isNotEmpty())
                <div class="fi-stats-grid-3">
                    @foreach($expensesByCategory as $item)
                        <div class="fi-card fi-flex-between">
                            <div>
                                <p class="fi-font-medium">{{ $item['category'] }}</p>
                                <p class="fi-text-xs fi-text-muted">{{ $item['count'] }} expense(s)</p>
                            </div>
                            <span class="fi-font-semibold fi-text-danger">{{ $baseCurrencyEnum?->format($item['amount']) }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="fi-text-muted fi-text-sm">No expenses recorded this month.</p>
            @endif
        </x-filament::section>

        {{-- Monthly Trend --}}
        <x-filament::section>
            <x-slot name="heading">
                <span class="fi-section-heading">
                    <x-filament::icon icon="heroicon-o-chart-bar" />
                    6-Month Trend ({{ $baseCurrency }})
                </span>
            </x-slot>

            <div style="overflow-x: auto;">
                <table class="fi-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="fi-text-right">Income</th>
                            <th class="fi-text-right">Expenses</th>
                            <th class="fi-text-right">Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthlyTrend as $month)
                            <tr>
                                <td class="fi-font-medium">{{ $month['month'] }}</td>
                                <td class="fi-text-right fi-text-success">{{ $baseCurrencyEnum?->format($month['income']) }}</td>
                                <td class="fi-text-right fi-text-danger">{{ $baseCurrencyEnum?->format($month['expenses']) }}</td>
                                <td class="fi-text-right fi-font-semibold {{ $month['net'] >= 0 ? 'fi-text-success' : 'fi-text-danger' }}">{{ $baseCurrencyEnum?->format($month['net']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <div class="fi-stats-grid-2">
            {{-- Recent Income --}}
            <x-filament::section>
                <x-slot name="heading">
                    <span class="fi-section-heading">
                        <x-filament::icon icon="heroicon-o-arrow-down-tray" />
                        Recent Income
                    </span>
                </x-slot>

                @if($recentIncome->isNotEmpty())
                    <div class="fi-space-y-3">
                        @foreach($recentIncome as $entry)
                            <div class="fi-card fi-flex-between">
                                <div>
                                    <p class="fi-font-medium">{{ $entry['source'] }}</p>
                                    <p class="fi-text-xs fi-text-muted">{{ $entry['date'] }}</p>
                                </div>
                                <div class="fi-text-right">
                                    <p class="fi-font-semibold fi-text-success">{{ $entry['amount'] }}</p>
                                    @if(!$entry['is_received'])
                                        <span class="fi-badge fi-badge-warning">Pending</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="fi-text-muted fi-text-sm">No recent income entries.</p>
                @endif
            </x-filament::section>

            {{-- Recent Expenses --}}
            <x-filament::section>
                <x-slot name="heading">
                    <span class="fi-section-heading">
                        <x-filament::icon icon="heroicon-o-arrow-up-tray" />
                        Recent Expenses
                    </span>
                </x-slot>

                @if($recentExpenses->isNotEmpty())
                    <div class="fi-space-y-3">
                        @foreach($recentExpenses as $expense)
                            <div class="fi-card fi-flex-between">
                                <div>
                                    <p class="fi-font-medium">{{ $expense['title'] }}</p>
                                    <p class="fi-text-xs fi-text-muted">{{ $expense['category'] }} · {{ $expense['date'] }}</p>
                                </div>
                                <p class="fi-font-semibold fi-text-danger">{{ $expense['amount'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="fi-text-muted fi-text-sm">No recent expenses.</p>
                @endif
            </x-filament::section>
        </div>

        {{-- Upcoming Subscription Renewals --}}
        @if($upcomingRenewals->isNotEmpty())
            <x-filament::section>
                <x-slot name="heading">
                    <span class="fi-section-heading fi-text-warning">
                        <x-filament::icon icon="heroicon-o-bell-alert" />
                        Upcoming Subscription Renewals (Next 7 Days)
                    </span>
                </x-slot>

                <div class="fi-stats-grid-3">
                    @foreach($upcomingRenewals as $renewal)
                        <div class="fi-card fi-card-warning fi-flex-between">
                            <div>
                                <p class="fi-font-medium">{{ $renewal['title'] }}</p>
                                <p class="fi-text-xs fi-text-muted">{{ $renewal['next_billing_date'] }}</p>
                            </div>
                            <div class="fi-text-right">
                                <p class="fi-font-semibold">{{ $renewal['amount'] }}</p>
                                <p class="fi-text-xs fi-text-warning">in {{ $renewal['days_until'] }} day(s)</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
