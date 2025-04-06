<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModuleAttendantTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_all_attendants_in_module_ok(): void
    {
        $module = \App\Models\Module::factory()->create();
        $response = $this->get(route('modules.attendants.index', $module->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'dni',
                    'enabled',
                    'attention_profile',
                ],
            ],
        ]);
    }

    public function test_get_all_attendants_in_module_not_found(): void
    {
        $response = $this->get(route('modules.attendants.index', 100));
        $response->assertStatus(404);
    }
}
