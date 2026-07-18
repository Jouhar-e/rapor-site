<?php

namespace App\Filament\Widgets;

use App\Services\DashboardService;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class AdminCharts extends ChartWidget
{
    protected ?string $heading = 'Distribusi Program';

    // Tambahkan baris ini agar grafik tetap ringkas dan tidak terlalu tinggi
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
        $learnerByProgram = $chartData['learner_by_program'];

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Warga Belajar',
                    'data' => array_values($learnerByProgram),
                    'backgroundColor' => ['#3b82f6', '#22c55e', '#eab308', '#a855f7'],
                    'borderRadius' => 4,
                ],
            ],
            'labels' => array_keys($learnerByProgram),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['stepSize' => 1],
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }
}
