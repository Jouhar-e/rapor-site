<?php

namespace App\Models;

use App\Models\Concerns\HasAcademicYearScope;
use Database\Factories\HomeroomNoteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['learner_id', 'academic_year_id', 'semester_id', 'note', 'created_by'])]
class HomeroomNote extends Model
{
    use HasAcademicYearScope;

    /** @use HasFactory<HomeroomNoteFactory> */
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
}
