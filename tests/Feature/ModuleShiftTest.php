<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModuleShiftTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_module_current_shift_ok(): void
    {
        $module = \App\Models\Module::factory()->create();
        \App\Models\Shift::factory()->create([
            'state' => \App\Enums\ShiftState::InProgress,
            'module_id' => $module->id,
        ]);
        $response = $this->get(route(
            'modules.current-shift',
            ['module' => $module->id]
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
        $module = \App\Models\Module::factory()->create();
        $response = $this->get(route(
            'modules.current-shift',
            ['module' => $module->id]
        ));


        $response->assertStatus(204);
    }
}
