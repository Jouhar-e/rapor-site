<?php

namespace Database\Factories;

use App\Models\GradingSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GradingSetting>
 */
class GradingSettingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'task_percentage' => 40,
            'pts_percentage' => 20,
            'pas_percentage' => 25,
            'practice_percentage' => 15,
            'min_score' => 0,
            'max_score' => 100,
            'rounding_digits' => 0,
        ];
    }
}
