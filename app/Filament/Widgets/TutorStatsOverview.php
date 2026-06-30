<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\ClassLearner;
use App\Models\Grade;
use App\Models\HomeroomTeacher;
use App\Models\Semester;
use App\Services\DashboardService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TutorStatsOverview extends StatsOverviewWidget
{
    protected function getColumns(): int
    {
        return 4;
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        $stats = app(DashboardService::class)->getTutorStats($user);

        $activeSemester = Semester::whereHas(
            'academicYear',
            fn ($q) => $q->where('is_active', true)
        )->where('is_active', true)->first();

        $homeroomClassIds = HomeroomTeacher::where('user_id', $user->id)
            ->when($activeSemester, fn ($q) => $q->whereHas('academicYear', fn ($q) => $q->where('is_active', true)))
            ->pluck('class_id');

        $learnerIds = $homeroomClassIds->isNotEmpty()
            ? ClassLearner::whereIn('class_id', $homeroomClassIds)
                ->when($activeSemester, fn ($q) => $q->whereHas('academicYear', fn ($q) => $q->where('is_active', true)))
                ->pluck('learner_id')
            : collect();

        $incompleteGrades = $activeSemester && $learnerIds->isNotEmpty()
            ? Grade::whereIn('learner_id', $learnerIds)
                ->where('semester_id', $activeSemester->id)
                ->where('status', 'draft')
                ->count()
            : 0;

        $totalLearners = $learnerIds->count();
        $completedAttendance = $activeSemester && $learnerIds->isNotEmpty()
            ? Attendance::whereIn('learner_id', $learnerIds)
                ->where('semester_id', $activeSemester->id)
                ->distinct('learner_id')
                ->count('learner_id')
            : 0;

        $incompleteAttendance = max(0, $totalLearners - $completedAttendance);

        return [
            Stat::make('Total Kelas', $stats['total_classes'])
                ->description('Kelas yang diampu')
                ->icon('heroicon-o-building-library')
                ->color('info'),
            Stat::make('Warga Belajar', $stats['total_learners'])
                ->description('Warga binaan')
                ->icon('heroicon-o-users')
                ->color('success'),
            Stat::make('Nilai Perlu Dilengkapi', $incompleteGrades)
                ->description('Nilai masih draft')
                ->icon('heroicon-o-document-text')
                ->color('warning'),
            Stat::make('Absensi Perlu Dilengkapi', $incompleteAttendance)
                ->description('WB belum diisi absensinya')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('danger'),
        ];
    }

    private function emptyStats(): array
    {
        return [
            Stat::make('Total Kelas', 0)
                ->description('Kelas yang diampu')
                ->icon('heroicon-o-building-library')
                ->color('info'),
            Stat::make('Warga Belajar', 0)
                ->description('Warga binaan')
                ->icon('heroicon-o-users')
                ->color('success'),
            Stat::make('Nilai Perlu Dilengkapi', 0)
                ->description('Nilai masih draft')
                ->icon('heroicon-o-document-text')
                ->color('warning'),
            Stat::make('Absensi Perlu Dilengkapi', 0)
                ->description('WB belum diisi absensinya')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('danger'),
        ];
    }

    public static function canView(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return $user->hasRole('tutor') || $user->hasRole('admin');
    }
}
