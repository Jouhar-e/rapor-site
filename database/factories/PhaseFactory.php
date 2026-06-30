<?php

namespace Database\Factories;

use App\Models\Phase;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhaseFactory extends Factory
{
    protected $model = Phase::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->randomElement(['A', 'B', 'C', 'D', 'E', 'F']),
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
