<?php

namespace App\Models;

use Database\Factories\SubjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['program_id', 'class_id', 'subject_group_id', 'code', 'name', 'description', 'is_active'])]
class Subject extends Model
{
    /** @use HasFactory<SubjectFactory> */
    use HasFactory;

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function classes(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function subjectGroup(): BelongsTo
    {
        return $this->belongsTo(SubjectGroup::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
