<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Grade;
use App\Models\Learner;
use App\Models\Semester;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    public function __construct(
        protected Learner $learner,
        protected Tutor $tutor,
        protected Classes $classes,
        protected Grade $grade,
        protected Attendance $attendance,
        protected AcademicYear $academicYear,
        protected Semester $semester,
    ) {}

    public function getAdminStats(): array
    {
        $activeYear = $this->academicYear->where('is_active', true)->first();
        $activeSemester = $activeYear
            ? $this->semester->where('academic_year_id', $activeYear->id)->where('is_active', true)->first()
            : null;

        $learnerCount = $this->learner->where('status', 'aktif')->count();
        $tutorCount = $this->tutor->where('is_active', true)->count();
        $classCount = $this->classes->where('status', 'aktif')->count();

        $gradeCount = 0;
        $publishedGradeCount = 0;
        $attendanceSummary = ['sick' => 0, 'permission' => 0, 'absent' => 0];

        if ($activeSemester) {
            $gradeCount = $this->grade
                ->where('semester_id', $activeSemester->id)
                ->count();

            $publishedGradeCount = $this->grade
                ->where('semester_id', $activeSemester->id)
                ->where('status', 'published')
                ->count();

            $attendanceRecords = $this->attendance
                ->where('semester_id', $activeSemester->id)
                ->get();

            $attendanceSummary = [
                'sick' => $attendanceRecords->sum('sick'),
                'permission' => $attendanceRecords->sum('permission'),
                'absent' => $attendanceRecords->sum('absent'),
            ];
        }

        return [
            'total_learners' => $learnerCount,
            'active_learners' => $learnerCount,
            'total_tutors' => $tutorCount,
            'active_tutors' => $tutorCount,
            'total_classes' => $classCount,
            'active_classes' => $classCount,
            'total_grades' => $gradeCount,
            'published_grades' => $publishedGradeCount,
            'attendance' => $attendanceSummary,
            'academic_year' => $activeYear?->name,
            'semester' => $activeSemester?->name,
        ];
    }

    public function getTutorStats(User $user): array
    {
        $homeroomClasses = $user->homeroomTeachers()
            ->with('classes')
            ->get();

        if ($homeroomClasses->isEmpty()) {
            return [
                'total_classes' => 0,
                'total_subjects' => 0,
                'total_learners' => 0,
                'total_grades' => 0,
                'pending_grades' => 0,
            ];
        }

        return Cache::remember("tutor_stats_{$user->id}", 300, function () use ($homeroomClasses) {
            $activeYear = $this->academicYear->where('is_active', true)->first();
            $activeSemester = $activeYear
                ? $this->semester->where('academic_year_id', $activeYear->id)->where('is_active', true)->first()
                : null;

            $classIds = $homeroomClasses->pluck('classes.id')->filter()->values();

            $learnerCount = $classIds->isNotEmpty()
                ? $this->learner->whereHas('classLearners', fn ($q) => $q->whereIn('class_id', $classIds))->count()
                : 0;

            $gradeCount = 0;
            $pendingGradeCount = 0;

            if ($activeSemester && $classIds->isNotEmpty()) {
                $learnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');

                $gradeCount = $this->grade
                    ->whereIn('learner_id', $learnerIds)
                    ->where('semester_id', $activeSemester->id)
                    ->count();

                $pendingGradeCount = $this->grade
                    ->whereIn('learner_id', $learnerIds)
                    ->where('semester_id', $activeSemester->id)
                    ->where('status', 'draft')
                    ->count();
            }

            return [
                'total_classes' => $homeroomClasses->count(),
                'total_subjects' => $classIds->isNotEmpty()
                    ? $classIds->map(fn ($id) => $this->classes->find($id)?->subjects()->count())->sum()
                    : 0,
                'total_learners' => $learnerCount,
                'total_grades' => $gradeCount,
                'pending_grades' => $pendingGradeCount,
            ];
        });
    }

    public function getAdminCharts(): array
    {
        $activeYear = $this->academicYear->where('is_active', true)->first();

        $learnerByProgram = $this->learner
            ->selectRaw('program_id, count(*) as total')
            ->where('status', 'aktif')
            ->groupBy('program_id')
            ->with('program:id,name')
            ->get()
            ->pluck('total', 'program.name')
            ->toArray();

        $gradeDistribution = [];
        if ($activeYear) {
            $gradeDistribution = $this->grade
                ->where('academic_year_id', $activeYear->id)
                ->selectRaw("
                    CASE
                        WHEN final_score >= 90 THEN 'A'
                        WHEN final_score >= 80 THEN 'B'
                        WHEN final_score >= 70 THEN 'C'
                        WHEN final_score >= 60 THEN 'D'
                        ELSE 'E'
                    END as grade_letter,
                    count(*) as total
                ")
                ->groupBy('grade_letter')
                ->pluck('total', 'grade_letter')
                ->toArray();
        }

        $monthlyAttendance = $this->attendance
            ->selectRaw('
                MONTH(created_at) as month,
                SUM(sick) as sick,
                SUM(permission) as permission,
                SUM(absent) as absent
            ')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->get()
            ->toArray();

        return [
            'learner_by_program' => $learnerByProgram,
            'grade_distribution' => $gradeDistribution,
            'monthly_attendance' => $monthlyAttendance,
        ];
    }

    public function getAdminProgress(): array
    {
        $activeSemester = $this->semester
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->where('is_active', true)
            ->first();

        $gradeProgress = ['completed' => 0, 'pending' => 0, 'not_started' => 0, 'percentage' => 0];
        $attendanceProgress = ['completed' => 0, 'pending' => 0, 'not_started' => 0, 'percentage' => 0];

        if ($activeSemester) {
            $publishedGrades = $this->grade
                ->where('semester_id', $activeSemester->id)
                ->where('status', 'published')
                ->count();

            $draftGrades = $this->grade
                ->where('semester_id', $activeSemester->id)
                ->where('status', 'draft')
                ->count();

            $totalExpected = $publishedGrades + $draftGrades + $this->estimateMissingGrades($activeSemester->id);

            $gradeProgress = [
                'completed' => $publishedGrades,
                'pending' => $draftGrades,
                'not_started' => max(0, $totalExpected - $publishedGrades - $draftGrades),
                'percentage' => $totalExpected > 0 ? round(($publishedGrades / $totalExpected) * 100) : 0,
            ];

            $activeLearners = $this->learner->where('status', 'aktif')->count();

            $learnersWithAttendance = $activeLearners > 0
                ? $this->attendance
                    ->where('semester_id', $activeSemester->id)
                    ->distinct('learner_id')
                    ->count('learner_id')
                : 0;

            $attendanceProgress = [
                'completed' => $learnersWithAttendance,
                'pending' => 0,
                'not_started' => max(0, $activeLearners - $learnersWithAttendance),
                'percentage' => $activeLearners > 0 ? round(($learnersWithAttendance / $activeLearners) * 100) : 0,
            ];
        }

        return [
            'grade' => $gradeProgress,
            'attendance' => $attendanceProgress,
        ];
    }

    private function estimateMissingGrades(int $semesterId): int
    {
        $activeLearnerIds = $this->learner
            ->where('status', 'aktif')
            ->pluck('id');

        if ($activeLearnerIds->isEmpty()) {
            return 0;
        }

        $existingGradeLearnerIds = $this->grade
            ->where('semester_id', $semesterId)
            ->whereIn('learner_id', $activeLearnerIds)
            ->distinct('learner_id')
            ->pluck('learner_id');

        $learnersWithoutGrades = $activeLearnerIds->diff($existingGradeLearnerIds);

        return $learnersWithoutGrades->count();
    }

    public function getNotifications(): array
    {
        $activeSemester = $this->semester
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->where('is_active', true)
            ->first();

        $notifications = [];

        $pendingGradesCount = $activeSemester
            ? $this->grade->where('semester_id', $activeSemester->id)->where('status', 'draft')->count()
            : 0;

        if ($pendingGradesCount > 0) {
            $notifications[] = [
                'type' => 'warning',
                'message' => "{$pendingGradesCount} nilai masih dalam status draft.",
            ];
        }

        $incompleteAttendance = $this->learner
            ->where('status', 'aktif')
            ->whereDoesntHave('attendances', function ($q) use ($activeSemester): void {
                $q->where('semester_id', $activeSemester?->id);
            })
            ->count();

        if ($incompleteAttendance > 0) {
            $notifications[] = [
                'type' => 'info',
                'message' => "{$incompleteAttendance} peserta didik belum memiliki data absensi.",
            ];
        }

        return $notifications;
    }
}
