<?php

namespace Database\Factories;

use App\Models\Classes;
use App\Models\PromotionMapping;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromotionMapping>
 */
class PromotionMappingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source_class_id' => Classes::factory(),
            'destination_class_id' => Classes::factory(),
        ];
    }
}
