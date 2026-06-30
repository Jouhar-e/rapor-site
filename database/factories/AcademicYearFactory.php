<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicYear>
 */
class AcademicYearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->numberBetween(2020, 2030).'/'.fake()->numberBetween(2021, 2031),
            'start_date' => fake()->date(),
            'end_date' => fake()->date(),
            'is_active' => fake()->boolean(80),
            'is_archived' => false,
        ];
    }
}
