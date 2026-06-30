<?php

namespace Database\Factories;

use App\Models\Classes;
use App\Models\Program;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subject>
 */
class SubjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'class_id' => Classes::factory(),
            'subject_group_id' => null,
            'code' => fake()->unique()->bothify('SUB-####'),
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
