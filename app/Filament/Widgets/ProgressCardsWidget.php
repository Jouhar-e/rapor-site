<?php

namespace App\Filament\Widgets;

use App\Services\DashboardService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ProgressCardsWidget extends Widget
{
    protected string $view = 'filament.widgets.progress-cards';

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
        $progress = app(DashboardService::class)->getAdminProgress();

        return [
            'grade' => $progress['grade'],
            'attendance' => $progress['attendance'],
        ];
    }
}
