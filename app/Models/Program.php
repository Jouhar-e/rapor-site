<?php

namespace App\Models;

use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code',
    'name',
    'description',
    'is_active',
])]
class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory;

    public function learners(): HasMany
    {
        return $this->hasMany(Learner::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(Classes::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }
}
