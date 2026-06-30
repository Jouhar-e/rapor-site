<?php

namespace App\Models;

use Database\Factories\ClassesFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'program_id',
    'phase_id',
    'name',
    'description',
    'status',
])]
class Classes extends Model
{
    /** @use HasFactory<ClassesFactory> */
    use HasFactory;

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function homeroomTeachers(): HasMany
    {
        return $this->hasMany(HomeroomTeacher::class);
    }

    public function classLearners(): HasMany
    {
        return $this->hasMany(ClassLearner::class, 'class_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    public function sourcePromotionMappings(): HasMany
    {
        return $this->hasMany(PromotionMapping::class, 'source_class_id');
    }

    public function destinationPromotionMappings(): HasMany
    {
        return $this->hasMany(PromotionMapping::class, 'destination_class_id');
    }
}
