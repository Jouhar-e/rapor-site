<?php

namespace App\Models;

use Database\Factories\AcademicYearFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'start_date',
    'end_date',
    'is_active',
    'is_archived',
])]
class AcademicYear extends Model
{
    /** @use HasFactory<AcademicYearFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
            'is_archived' => 'boolean',
        ];
    }

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    public function homeroomTeachers(): HasMany
    {
        return $this->hasMany(HomeroomTeacher::class);
    }

    public function classLearners(): HasMany
    {
        return $this->hasMany(ClassLearner::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_archived) {
            return 'archived';
        }

        if ($this->is_active) {
            return 'active';
        }

        return 'inactive';
    }
}
