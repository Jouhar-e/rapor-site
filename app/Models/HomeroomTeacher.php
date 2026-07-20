<?php

namespace App\Models;

use App\Models\Concerns\HasAcademicYearScope;
use Database\Factories\HomeroomTeacherFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'class_id', 'academic_year_id'])]
class HomeroomTeacher extends Model
{
    use HasAcademicYearScope;

    /** @use HasFactory<HomeroomTeacherFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classes(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
