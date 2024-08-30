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
        $dni = $this->faker->unique()->randomNumber(8) . $this->faker->randomNumber(2);
        $dni = strval($dni);
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'dni' => $dni,
            'password' => bcrypt($dni),
            'enabled' => true,
            'status' => 'free',
        ];
    }
}
