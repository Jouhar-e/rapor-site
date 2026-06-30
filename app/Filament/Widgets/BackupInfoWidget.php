<?php

namespace App\Filament\Widgets;

use App\Models\BackupHistory;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class BackupInfoWidget extends StatsOverviewWidget
{
    protected function getColumns(): int
    {
        return 2;
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    protected function getStats(): array
    {
        $lastBackup = BackupHistory::latest()->first();

        return [
            Stat::make('Backup Terakhir', $lastBackup?->created_at?->diffForHumans() ?? 'Belum Pernah')
                ->description($lastBackup ? $lastBackup->filename : 'Tidak ada backup')
                ->icon('heroicon-o-archive-box')
                ->color($lastBackup ? 'success' : 'warning'),
            Stat::make('Total Backup', BackupHistory::count())
                ->description('Seluruh riwayat backup')
                ->icon('heroicon-o-circle-stack')
                ->color('info'),
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }
}
