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
        $dni = $this->faker->unique()->numberBetween(1000000000, 9999999999);
        $dni = strval($dni);
        return [
            'name' => $this->faker->name,
            'dni' => $dni,
            'client_type_id' => \App\Models\ClientType::factory(),
        ];
    }
}
