<?php

namespace App\Services;

use App\Models\ClassLearner;
use App\Models\Grade;
use App\Models\GradePredicate;
use App\Models\GradingSetting;
use App\Models\HomeroomTeacher;

class GradeService
{
    public function __construct(
        protected GradingSetting $gradingSetting,
        protected GradePredicate $gradePredicate,
        protected CompetencyService $competencyService,
    ) {}

    public function calculateFinalScore(
        float $task,
        float $pts,
        float $pas,
        float $practice,
    ): float {
        $setting = $this->gradingSetting->first();

        if (! $setting) {
            return round(($task + $pts + $pas + $practice) / 4, 2);
        }

        $total = (
            ($task * $setting->task_percentage / 100) +
            ($pts * $setting->pts_percentage / 100) +
            ($pas * $setting->pas_percentage / 100) +
            ($practice * $setting->practice_percentage / 100)
        );

        return round($total, $setting->rounding_digits ?? 2);
    }

    public function determinePredicate(float $finalScore): string
    {
        $predicate = $this->gradePredicate
            ->where('min_score', '<=', $finalScore)
            ->where('max_score', '>=', $finalScore)
            ->first();

        return $predicate?->predicate ?? 'Tidak Memadai';
    }

    public function calculateAndSave(Grade $grade): Grade
    {
        $finalScore = $this->calculateFinalScore(
            (float) $grade->task_score,
            (float) $grade->pts_score,
            (float) $grade->pas_score,
            (float) $grade->practice_score,
        );

        $predicate = $this->determinePredicate($finalScore);

        $grade->final_score = $finalScore;
        $grade->predicate = $predicate;
        $grade->save();

        $grade = $this->competencyService->generateAndSave($grade);

        return $grade;
    }

    public function publishGrade(Grade $grade): void
    {
        $grade->status = 'published';
        $grade->save();

        app(AuditService::class)->logPublish(Grade::class, $grade->id);
    }

    public function lockGrade(Grade $grade): void
    {
        $grade->status = 'locked';
        $grade->save();

        app(AuditService::class)->logLock(Grade::class, $grade->id);
    }

    public function publishGradesForClass(int $classId, int $academicYearId, int $semesterId): int
    {
        $learnerIds = ClassLearner::where('class_id', $classId)
            ->where('academic_year_id', $academicYearId)
            ->pluck('learner_id');

        $count = Grade::whereIn('learner_id', $learnerIds)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->where('status', 'draft')
            ->update(['status' => 'published']);

        $homeroomTeachers = HomeroomTeacher::where('class_id', $classId)
            ->where('academic_year_id', $academicYearId)
            ->with('user')
            ->get();

        foreach ($homeroomTeachers as $ht) {
            if ($ht->user) {
                app(NotificationService::class)->createNotification(
                    'success',
                    $ht->user,
                    ['message' => 'Nilai untuk kelas sudah dipublikasikan oleh admin.'],
                );
            }
        }

        return $count;
    }
}
