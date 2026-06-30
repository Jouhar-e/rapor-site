<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\HomeroomNote;
use App\Models\Learner;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HomeroomNote>
 */
class HomeroomNoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'learner_id' => Learner::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'semester_id' => Semester::factory(),
            'note' => fake()->paragraph(),
        ];
    }
}
