<?php

namespace App\Models;

use Database\Factories\CompetencyTemplateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['subject_id', 'predicate', 'achievement_text', 'improvement_text'])]
class CompetencyTemplate extends Model
{
    /** @use HasFactory<CompetencyTemplateFactory> */
    use HasFactory;

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
