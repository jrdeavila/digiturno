<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shift>
 */
class ShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => \App\Models\Client::factory(),
            'attention_profile_id' => \App\Models\AttentionProfile::factory(),
            'room_id' => \App\Models\Room::factory()->create([
                'branch_id' => \App\Models\Branch::factory(),
            ])->id,
            'state' => $this->faker->randomElement(['pending', 'distracted', 'in_progress', 'completed', 'qualified',]),
        ];
    }
}
