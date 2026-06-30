<?php

namespace App\Filament\Widgets;

use App\Models\Learner;
use App\Services\DashboardService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AdminStatsCardsWidget extends Widget
{
    protected string $view = 'filament.widgets.admin-stats-cards';

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }

    protected function getViewData(): array
    {
        $stats = app(DashboardService::class)->getAdminStats();

        return [
            'cards' => [
                [
                    'icon' => 'heroicon-o-users',
                    'label' => 'Total Tutor',
                    'value' => $stats['total_tutors'],
                    'trend' => '+1 bulan ini',
                    'color' => 'success',
                    'sparkline' => 'M0,22 L20,18 L40,12 L60,8 L80,3',
                    'sparklineFill' => 'M0,22 L20,18 L40,12 L60,8 L80,3 L80,28 L0,28 Z',
                ],
                [
                    'icon' => 'heroicon-o-user-group',
                    'label' => 'Total Warga Belajar',
                    'value' => $stats['total_learners'],
                    'trend' => '+2 bulan ini',
                    'color' => 'info',
                    'sparkline' => 'M0,20 L20,16 L40,10 L60,6 L80,2',
                    'sparklineFill' => 'M0,20 L20,16 L40,10 L60,6 L80,2 L80,28 L0,28 Z',
                ],
                [
                    'icon' => 'heroicon-o-check-circle',
                    'label' => 'Warga Aktif',
                    'value' => $stats['active_learners'],
                    'trend' => '100% aktif',
                    'color' => 'warning',
                    'sparkline' => 'M0,24 L20,20 L40,16 L60,10 L80,6',
                    'sparklineFill' => 'M0,24 L20,20 L40,16 L60,10 L80,6 L80,28 L0,28 Z',
                ],
                [
                    'icon' => 'heroicon-o-academic-cap',
                    'label' => 'Total Alumni',
                    'value' => Learner::where('status', 'alumni')->count(),
                    'trend' => 'Belum ada alumni',
                    'color' => 'primary',
                    'sparkline' => 'M0,22 L20,20 L40,18 L60,20 L80,16',
                    'sparklineFill' => 'M0,22 L20,20 L40,18 L60,20 L80,16 L80,28 L0,28 Z',
                ],
            ],
            'theme' => [
                'success' => ['bg' => '#d1fae5', 'icon' => '#059669', 'text' => '#059669', 'line' => '#10b981'],
                'info' => ['bg' => '#dbeafe', 'icon' => '#2563eb', 'text' => '#2563eb', 'line' => '#3b82f6'],
                'warning' => ['bg' => '#fef3c7', 'icon' => '#d97706', 'text' => '#d97706', 'line' => '#f59e0b'],
                'primary' => ['bg' => '#e0e7ff', 'icon' => '#4f46e5', 'text' => '#4f46e5', 'line' => '#6366f1'],
            ],
        ];
    }
}
