<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendant>
 */
class AttendantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dni = $this->faker->unique()->randomNumber(10);
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'dni' => $dni,
            'password' => $dni,
            'attention_profile_id' => \App\Models\AttentionProfile::factory(),
        ];
    }
}
