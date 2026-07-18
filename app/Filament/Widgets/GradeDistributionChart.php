<?php

namespace App\Filament\Widgets;

use App\Services\DashboardService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class GradeDistributionChart extends ChartWidget
{
    protected ?string $heading = 'Distribusi Nilai';

    // Tambahkan baris ini agar tingginya persis sama dengan grafik batang
    protected ?string $maxHeight = '275px';

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 'full',
            'md' => 6,
            'xl' => 6, // Seimbangkan menjadi 6 kolom (50% layar)
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

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => ['usePointStyle' => true],
                ],
            ],
            'cutout' => '65%',
            'maintainAspectRatio' => false, // Pastikan lingkaran tidak membesar sendiri
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }
}
