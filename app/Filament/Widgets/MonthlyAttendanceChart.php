<?php

namespace App\Filament\Widgets;

use App\Services\DashboardService;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class MonthlyAttendanceChart extends ChartWidget
{
    protected ?string $heading = 'Absensi Bulanan';

    public function getColumnSpan(): int|string|array
    {
        return 1;
    }

    protected function getData(): array
    {
        $chartData = app(DashboardService::class)->getAdminCharts();
        $monthlyAttendance = $chartData['monthly_attendance'];

        $months = collect($monthlyAttendance)->pluck('month')->map(fn ($m) => Carbon::create()->month($m)->translatedFormat('F'))->toArray();
        $sick = collect($monthlyAttendance)->pluck('sick')->toArray();
        $permission = collect($monthlyAttendance)->pluck('permission')->toArray();
        $absent = collect($monthlyAttendance)->pluck('absent')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Sakit',
                    'data' => $sick,
                    'backgroundColor' => '#f59e0b',
                    'borderColor' => '#f59e0b',
                ],
                [
                    'label' => 'Izin',
                    'data' => $permission,
                    'backgroundColor' => '#3b82f6',
                    'borderColor' => '#3b82f6',
                ],
                [
                    'label' => 'Alpa',
                    'data' => $absent,
                    'backgroundColor' => '#ef4444',
                    'borderColor' => '#ef4444',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }
}
