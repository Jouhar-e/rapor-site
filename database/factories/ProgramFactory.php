<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->randomElement(['PA', 'PB', 'PC']),
            'name' => fake()->unique()->randomElement(['Paket A', 'Paket B', 'Paket C']),
            'description' => fake()->sentence(),
            'is_active' => fake()->boolean(80),
        ];
    }
}
