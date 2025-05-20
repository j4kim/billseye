<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->email(),
            'name' => fake()->company(),
            'street' => fake()->streetName(),
            'building_number' => fake()->numberBetween(1, 112),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'country' => 'CH',
            'iban' => fake()->iban('CH'),
        ];
    }
}
