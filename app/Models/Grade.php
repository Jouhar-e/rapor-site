<?php

namespace App\Models;

use Database\Factories\GradeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'learner_id', 'subject_id', 'academic_year_id', 'semester_id',
    'task_score', 'pts_score', 'pas_score', 'practice_score',
    'final_score', 'predicate', 'description', 'competency_description', 'status',
])]
class Grade extends Model
{
    /** @use HasFactory<GradeFactory> */
    use HasFactory;

    public function learner(): BelongsTo
    {
        return $this->belongsTo(Learner::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
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
            'task_score' => 'decimal:2',
            'pts_score' => 'decimal:2',
            'pas_score' => 'decimal:2',
            'practice_score' => 'decimal:2',
            'final_score' => 'decimal:2',
            'status' => 'string',
        ];
    }
}
