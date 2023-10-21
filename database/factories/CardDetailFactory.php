<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\card_details>
 */
class CardDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'quantity' => fake()->randomDigit(),
            'time_duration' => fake()->randomNumber(2),
            'time_recovery' => fake()->randomNumber(2),
            'weight' => fake()->randomNumber(3),
            'notes' => fake()->text(100),
        ];
    }
}


