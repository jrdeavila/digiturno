<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModuleShiftTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void
    {
        parent::setUp();
        \App\Models\Module::factory()->create(['ip_address' => '0.0.0.0', 'enabled' => true]);
        $this->withHeaders(['X-Module-Ip' => '0.0.0.0']);
    }
    /**
     * A basic feature test example.
     */

    public function test_get_module_shifts_ok(): void
    {
        $module = \App\Models\Module::where('ip_address', '0.0.0.0')->first();
        \App\Models\Shift::factory()->count(5)->create([
            'module_id' => $module->id,
            'state' => \App\Enums\ShiftState::Pending,
        ]);
        \App\Models\Shift::factory()->count(5)->create([
            'module_id' => $module->id,
            'state' => \App\Enums\ShiftState::PendingTransferred,
        ]);

        $response = $this->get(route(
            'modules.my-shifts',
        ));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'room',
                    'attention_profile',
                    'client',
                    'state',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_get_module_processors_shifts_ok(): void
    {
        $clientType = \App\Models\ClientType::factory()->create(['priority' => 2]);
        $module = \App\Models\Module::factory()->create([
            'client_type_id' => $clientType->id,
            'enabled' => true,
        ]);

        $response = $this
            ->withHeaders(['X-Module-Ip' => $module->ip_address])
            ->get(route(
                'modules.my-shifts',
            ));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'room',
                    'attention_profile',
                    'client',
                    'state',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }


    public function test_get_module_current_shift_ok(): void
    {
        $module = \App\Models\Module::where('ip_address', '0.0.0.0')->first();
        \App\Models\Shift::factory()->create([
            'state' => \App\Enums\ShiftState::InProgress,
            'module_id' => $module->id,
        ]);
        $response = $this->get(route(
            'modules.current-shift',
        ));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'room',
                'attention_profile',
                'client',
                'state',
                'created_at',
                'updated_at',
            ],
        ]);
    }


    public function test_get_module_current_shift_not_found(): void
    {
        $response = $this->get(route(
            'modules.current-shift',
        ));


        $response->assertStatus(204);
    }
}
