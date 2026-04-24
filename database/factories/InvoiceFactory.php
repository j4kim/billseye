<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => now(),
            'subject' => fake()->sentence(),
            'state' => fake()->randomElement([
                'Creating',
                'Ready',
                'Sent',
                'Paid',
            ]),
            'layout' => implode('<br><br>', fake()->paragraphs(2)),
            'currency' => 'CHF',
            'amount' => fake()->randomNumber(3)
        ];
    }
}
