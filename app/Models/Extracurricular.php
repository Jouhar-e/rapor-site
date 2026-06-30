<?php

namespace App\Models;

use Database\Factories\ExtracurricularFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'description', 'is_active'])]
class Extracurricular extends Model
{
    /** @use HasFactory<ExtracurricularFactory> */
    use HasFactory;

    public function learnerExtracurriculars(): HasMany
    {
        return $this->hasMany(LearnerExtracurricular::class);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
