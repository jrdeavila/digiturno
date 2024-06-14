<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ex: 1234567890, 0987654321, 2345678901, 3456789012
        $dni = "1234567890";
        return [
            'name' => $this->faker->name,
            'dni' => $dni,
        ];
    }
}
