<?php

namespace App\Filament\Widgets;

use App\Models\AuditLog;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class RecentActivityWidget extends Widget
{
    protected string $view = 'filament.widgets.recent-activity';

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 'full',
            'md' => 2,
            'xl' => 4,
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }

    protected function getViewData(): array
    {
        return [
            'activities' => AuditLog::latest()->limit(5)->get(),
        ];
    }
}
