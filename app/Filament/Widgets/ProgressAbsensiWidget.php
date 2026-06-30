<?php

namespace App\Filament\Widgets;

use App\Services\DashboardService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ProgressAbsensiWidget extends Widget
{
    protected string $view = 'filament.widgets.progress-absensi';

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 'full',
            'md' => 1,
            'xl' => 6,
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }

    protected function getViewData(): array
    {
        return app(DashboardService::class)->getAdminProgress()['attendance'];
    }
}
