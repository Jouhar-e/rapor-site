<?php

namespace App\Filament\Widgets;

use App\Services\DashboardService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class GradeDistributionChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Nilai';

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 'full',
            'md' => 1,
            'xl' => 4,
        ];
    }

    protected function getData(): array
    {
        $chartData = app(DashboardService::class)->getAdminCharts();
        $gradeDistribution = $chartData['grade_distribution'];

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Warga Belajar',
                    'data' => array_values($gradeDistribution),
                    'backgroundColor' => ['#22c55e', '#3b82f6', '#eab308', '#ef4444', '#a855f7'],
                ],
            ],
            'labels' => array_keys($gradeDistribution),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }
}
