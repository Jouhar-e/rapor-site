<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminCharts;
use App\Filament\Widgets\AdminStatsCardsWidget;
use App\Filament\Widgets\GradeDistributionChart;
use App\Filament\Widgets\ProgressAbsensiWidget;
use App\Filament\Widgets\ProgressNilaiWidget;
use App\Filament\Widgets\QuickActionsWidget;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\TutorStatsOverview;
use App\Filament\Widgets\WelcomeWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    // Fungsi filtersForm() beserta seluruh trait-nya sudah dihapus total di sini

    public function getColumns(): int|array
    {
        return [
            'sm' => 1,
            'md' => 2,
            'xl' => 12,
        ];
    }

    public function getWidgets(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        return match (true) {
            $user->hasRole('admin') => $this->getAdminWidgets(),
            $user->hasRole('tutor') => $this->getTutorWidgets(),
            default => [],
        };
    }

    private function getAdminWidgets(): array
    {
        return [
            WelcomeWidget::class,
            AdminStatsCardsWidget::class,
            ProgressNilaiWidget::class,
            ProgressAbsensiWidget::class,
            AdminCharts::class,
            GradeDistributionChart::class,
            RecentActivityWidget::class,
            QuickActionsWidget::class,
        ];
    }

    private function getTutorWidgets(): array
    {
        return [
            WelcomeWidget::class,
            TutorStatsOverview::class,
            QuickActionsWidget::class,
        ];
    }
}
