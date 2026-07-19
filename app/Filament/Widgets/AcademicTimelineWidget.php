<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Semester;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AcademicTimelineWidget extends Widget
{
    protected string $view = 'filament.widgets.academic-timeline';

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 'full',
            'md' => 2,
            'xl' => 6,
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }

    protected function getViewData(): array
    {
        $years = AcademicYear::with('semesters')
            ->orderBy('start_date', 'desc')
            ->get();

        $activeYear = $years->firstWhere('is_active', true);
        $activeSemester = $activeYear
            ? Semester::where('academic_year_id', $activeYear->id)->where('is_active', true)->first()
            : null;

        $timeline = $years->map(function ($year) use ($activeSemester) {
            $semesters = $year->semesters->map(function ($sem) use ($activeSemester) {
                $gradeCount = Grade::where('semester_id', $sem->id)->count();
                $publishedCount = Grade::where('semester_id', $sem->id)->where('status', 'published')->count();
                $percentage = $gradeCount > 0 ? round(($publishedCount / $gradeCount) * 100) : 0;

                return [
                    'id' => $sem->id,
                    'name' => $sem->name,
                    'is_active' => $activeSemester && $sem->id === $activeSemester->id,
                    'grade_percentage' => $percentage,
                    'grade_total' => $gradeCount,
                    'grade_published' => $publishedCount,
                ];
            });

            return [
                'id' => $year->id,
                'name' => $year->name,
                'is_active' => $year->is_active,
                'start_date' => $year->start_date?->format('d M Y'),
                'end_date' => $year->end_date?->format('d M Y'),
                'semesters' => $semesters,
            ];
        });

        return [
            'timeline' => $timeline,
            'activeYear' => $activeYear?->name,
            'activeSemester' => $activeSemester?->name,
        ];
    }
}
