<?php

namespace App\Models;

use Database\Factories\LearnerExtracurricularFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['learner_id', 'extracurricular_id', 'academic_year_id', 'semester_id', 'predicate', 'description'])]
class LearnerExtracurricular extends Model
{
    /** @use HasFactory<LearnerExtracurricularFactory> */
    use HasFactory;

    public function learner(): BelongsTo
    {
        return $this->belongsTo(Learner::class);
    }

    public function extracurricular(): BelongsTo
    {
        return $this->belongsTo(Extracurricular::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }
}
