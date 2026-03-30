<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\ContactSubmission;
use App\Models\Project;
use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContentStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Published Projects', Project::published()->count())
                ->icon('heroicon-o-rectangle-stack')
                ->color('primary'),
            Stat::make('Published Articles', Article::published()->count())
                ->icon('heroicon-o-document-text')
                ->color('success'),
            Stat::make('Services', Service::count())
                ->icon('heroicon-o-briefcase')
                ->color('info'),
            Stat::make('Contact Leads', ContactSubmission::where('status', 'new')->count())
                ->description('Unread submissions')
                ->icon('heroicon-o-inbox')
                ->color('warning'),
        ];
    }
}
