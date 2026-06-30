<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Learner;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Builder;

class AttendanceService
{
    public function __construct(
        protected Attendance $attendance,
    ) {}

    public function getAttendanceSummary(
        Learner $learner,
        AcademicYear $year,
        Semester $semester,
    ): array {
        $records = $this->attendance
            ->where('learner_id', $learner->id)
            ->where('academic_year_id', $year->id)
            ->where('semester_id', $semester->id)
            ->get();

        return [
            'sick' => $records->sum('sick'),
            'permission' => $records->sum('permission'),
            'absent' => $records->sum('absent'),
            'total_days' => $records->count(),
        ];
    }

    public function isAttendanceComplete(
        Learner $learner,
        AcademicYear $year,
        Semester $semester,
    ): bool {
        return $this->attendance
            ->where('learner_id', $learner->id)
            ->where('academic_year_id', $year->id)
            ->where('semester_id', $semester->id)
            ->where(function (Builder $query): void {
                $query->whereNull('sick')
                    ->orWhereNull('permission')
                    ->orWhereNull('absent');
            })
            ->doesntExist();
    }
}
