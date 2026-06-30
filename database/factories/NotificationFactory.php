<?php

namespace Database\Factories;

use App\Models\Learner;
use App\Models\Notification;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->word(),
            'notifiable_type' => fake()->randomElement([
                User::class,
                Tutor::class,
                Learner::class,
            ]),
            'notifiable_id' => fake()->numberBetween(1, 100),
            'data' => fake()->text(),
            'read_at' => fake()->optional()->dateTime(),
        ];
    }
}
