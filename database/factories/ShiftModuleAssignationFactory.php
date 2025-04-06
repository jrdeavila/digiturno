<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShiftModuleAssignation>
 */
class ShiftModuleAssignationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'module_id' => \App\Models\Module::factory(),
            'shift_id' => \App\Models\Shift::factory(),
            'status' => $this->faker->randomElement(['assigned', 'transferred', 'completed']),
        ];
    }
}
