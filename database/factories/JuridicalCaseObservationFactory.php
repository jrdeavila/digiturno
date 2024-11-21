<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JuridicalCaseObservation>
 */
class JuridicalCaseObservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->text,
            'juridical_case_id' => \App\Models\JuridicalCase::factory(),
            'attendant_id' => \App\Models\Attendant::factory(),
        ];
    }
}
