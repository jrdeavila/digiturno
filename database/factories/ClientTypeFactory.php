<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientType>
 */
class ClientTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->name;
        $slug = \Illuminate\Support\Str::slug($name);
        return [
            'name' => $name,
            'slug' => $slug,
            'priority' => $this->faker->numberBetween(1, 3),
        ];
    }
}
