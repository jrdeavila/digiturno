<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JuridicalCase>
 */
class JuridicalCaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject' => $this->faker->sentence,
            'client_id' => \App\Models\Client::factory(),
            'attendant_id' => \App\Models\Attendant::factory(),
            'status' => 'open',
        ];
    }
}
