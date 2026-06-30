<?php

namespace App\Models;

use Database\Factories\LearnerReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['learner_id', 'academic_year_id', 'semester_id', 'report_number', 'issued_date', 'status'])]
class LearnerReport extends Model
{
    /** @use HasFactory<LearnerReportFactory> */
    use HasFactory;

    public function learner(): BelongsTo
    {
        return $this->belongsTo(Learner::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    protected function casts(): array
    {
        return [
            'issued_date' => 'date',
        ];
    }
}
