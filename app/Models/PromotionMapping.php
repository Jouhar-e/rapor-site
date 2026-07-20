<?php

namespace App\Models;

use App\Models\Concerns\HasAcademicYearScope;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['source_class_id', 'destination_class_id', 'academic_year_id', 'promoted_at', 'notes'])]
class PromotionMapping extends Model
{
    use HasAcademicYearScope;

    protected function casts(): array
    {
        return [
            'promoted_at' => 'datetime',
        ];
    }

    public function sourceClass(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'source_class_id');
    }

    public function destinationClass(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'destination_class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
