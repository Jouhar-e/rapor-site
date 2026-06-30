<?php

namespace App\Models;

use Database\Factories\GradingSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'task_percentage', 'pts_percentage', 'pas_percentage',
    'practice_percentage', 'min_score', 'max_score', 'rounding_digits',
])]
class GradingSetting extends Model
{
    /** @use HasFactory<GradingSettingFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'rounding_digits' => 'integer',
        ];
    }
}
