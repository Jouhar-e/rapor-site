<?php

namespace App\Models;

use Database\Factories\ClassLearnerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['learner_id', 'class_id', 'academic_year_id'])]
class ClassLearner extends Model
{
    /** @use HasFactory<ClassLearnerFactory> */
    use HasFactory;

    public function learner(): BelongsTo
    {
        return $this->belongsTo(Learner::class);
    }

    public function classes(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
