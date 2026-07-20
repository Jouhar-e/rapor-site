<?php

namespace App\Models;

use App\Models\Concerns\HasAcademicYearScope;
use Database\Factories\SemesterFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'academic_year_id',
    'name',
    'is_active',
])]
class Semester extends Model
{
    use HasAcademicYearScope;

    /** @use HasFactory<SemesterFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function learnerExtracurriculars(): HasMany
    {
        return $this->hasMany(LearnerExtracurricular::class);
    }

    public function homeroomNotes(): HasMany
    {
        return $this->hasMany(HomeroomNote::class);
    }
}
