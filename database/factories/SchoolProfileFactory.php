<?php

namespace Database\Factories;

use App\Models\SchoolProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolProfile>
 */
class SchoolProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' PKBM',
            'npsn' => fake()->unique()->numerify('##########'),
            'address' => fake()->address(),
            'district' => fake()->city(),
            'city' => fake()->city(),
            'province' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'website' => fake()->url(),
            'logo' => null,
            'headmaster_name' => fake()->name(),
            'headmaster_nip' => fake()->numerify('##################'),
            'headmaster_signature' => null,
            'school_stamp' => null,
            'description' => fake()->paragraph(),
        ];
    }
}
