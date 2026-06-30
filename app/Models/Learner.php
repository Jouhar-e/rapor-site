<?php

namespace App\Models;

use Database\Factories\LearnerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'program_id',
    'nis',
    'nisn',
    'name',
    'gender',
    'birth_place',
    'birth_date',
    'address',
    'status',
    'religion',
    'child_order',
    'phone',
    'admission_date',
    'admission_class',
    'admission_status',
    'father_name',
    'father_job',
    'mother_name',
    'mother_job',
    'guardian_name',
    'guardian_job',
    'report_number',
])]
class Learner extends Model
{
    /** @use HasFactory<LearnerFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'admission_date' => 'date',
            'child_order' => 'integer',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function classLearners(): HasMany
    {
        return $this->hasMany(ClassLearner::class);
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
