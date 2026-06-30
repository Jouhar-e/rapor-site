<?php

namespace App\Filament\Widgets;

use App\Models\Learner;
use App\Services\DashboardService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected function getColumns(): int
    {
        return 4;
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    protected function getStats(): array
    {
        $stats = app(DashboardService::class)->getAdminStats();

        return [
            Stat::make('Total Tutor', $stats['total_tutors'])
                ->description('+1 bulan ini')
                ->icon('heroicon-o-users')
                ->color('success'),
            Stat::make('Total Warga Belajar', $stats['total_learners'])
                ->description('+2 bulan ini')
                ->icon('heroicon-o-user-group')
                ->color('info'),
            Stat::make('Warga Aktif', $stats['active_learners'])
                ->description('100% aktif')
                ->icon('heroicon-o-check-circle')
                ->color('warning'),
            Stat::make('Total Alumni', Learner::where('status', 'alumni')->count())
                ->description('Belum ada alumni')
                ->icon('heroicon-o-academic-cap')
                ->color('primary'),
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }
}
