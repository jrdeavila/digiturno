<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Module>
 */
class ModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'ip_address' => $this->faker->ipv4,
            'room_id' => \App\Models\Room::factory()->create([
                'branch_id' => \App\Models\Branch::factory(),
            ]),
            'client_type_id' => \App\Models\ClientType::factory(),
            'attention_profile_id' => \App\Models\AttentionProfile::factory(),
            'enabled' => true,
            'module_type_id' => \App\Models\ModuleType::factory(),
        ];
    }
}
