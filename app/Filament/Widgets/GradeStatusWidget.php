<?php

namespace App\Filament\Widgets;

use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Grade;
use App\Models\Semester;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class GradeStatusWidget extends Widget
{
    protected string $view = 'filament.widgets.grade-status';

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
        $activeSemester = Semester::whereHas(
            'academicYear',
            fn ($q) => $q->where('is_active', true)
        )->where('is_active', true)->first();

        if (! $activeSemester) {
            return ['classes' => collect(), 'semesterName' => null];
        }

        $classes = Classes::where('status', 'aktif')
            ->with('program', 'phase')
            ->get()
            ->map(function ($class) use ($activeSemester) {
                $learnerIds = ClassLearner::where('class_id', $class->id)
                    ->where('academic_year_id', $activeSemester->academic_year_id)
                    ->pluck('learner_id');

                $totalLearners = $learnerIds->count();

                $grades = Grade::whereIn('learner_id', $learnerIds)
                    ->where('semester_id', $activeSemester->id)
                    ->selectRaw('learner_id, status, count(*) as total')
                    ->groupBy('learner_id', 'status')
                    ->get();

                $statuses = $grades->groupBy('status')->map->count();
                $published = $statuses->get('published', 0);
                $draft = $statuses->get('draft', 0);
                $locked = $statuses->get('locked', 0);
                $completed = $published + $locked;

                $completedLearners = $grades->whereIn('status', ['published', 'locked'])->pluck('learner_id')->unique()->count();
                $draftLearners = $grades->where('status', 'draft')->pluck('learner_id')->unique()->count();
                $notStarted = max(0, $totalLearners - $completedLearners - $draftLearners);

                $percentage = $totalLearners > 0 ? round(($completedLearners / $totalLearners) * 100) : 0;

                return [
                    'id' => $class->id,
                    'name' => $class->name,
                    'program' => $class->program?->name,
                    'phase' => $class->phase?->name,
                    'total_learners' => $totalLearners,
                    'completed' => $completedLearners,
                    'draft' => $draftLearners,
                    'not_started' => $notStarted,
                    'percentage' => $percentage,
                    'status' => $percentage >= 100 ? 'complete' : ($percentage > 0 ? 'partial' : 'empty'),
                ];
            })
            ->sortByDesc('percentage')
            ->values();

        return [
            'classes' => $classes,
            'semesterName' => $activeSemester->name,
        ];
    }
}
