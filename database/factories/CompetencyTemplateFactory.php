<?php

namespace Database\Factories;

use App\Models\CompetencyTemplate;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompetencyTemplateFactory extends Factory
{
    protected $model = CompetencyTemplate::class;

    public function definition(): array
    {
        return [
            'subject_id' => Subject::factory(),
            'predicate' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'achievement_text' => '{nama} menunjukkan kompetensi yang {level} dalam memahami materi.',
            'improvement_text' => 'Perlu meningkatkan pemahaman pada materi.',
        ];
    }
}
