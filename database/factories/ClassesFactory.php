<?php

namespace Database\Factories;

use App\Models\Classes;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Classes>
 */
class ClassesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'phase_id' => null,
            'name' => fake()->randomElement(['A', 'B', 'C']).' - '.fake()->randomElement(['Pagi', 'Siang', 'Sore']),
            'description' => fake()->sentence(),
            'status' => 'aktif',
        ];
    }
}
