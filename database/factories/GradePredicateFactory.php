<?php

namespace Database\Factories;

use App\Models\GradePredicate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GradePredicate>
 */
class GradePredicateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'min_score' => fake()->randomFloat(2, 0, 50),
            'max_score' => fake()->randomFloat(2, 51, 100),
            'predicate' => fake()->randomElement(['A', 'B', 'C', 'D', 'E']),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
