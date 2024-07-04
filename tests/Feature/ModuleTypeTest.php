<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModuleTypeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_all_module_types_ok(): void
    {
        \App\Models\ModuleType::factory(5)->create();
        $response = $this->get(route('module_types.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                ],
            ],
        ]);
        $response->assertJsonCount(5, 'data');
    }

    public function test_get_only_one_module_type_ok(): void
    {
        $moduleType = \App\Models\ModuleType::factory()->create();
        $response = $this->get(route('module_types.show', $moduleType));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
            ],
        ]);
    }

    public function test_get_only_one_module_type_not_found(): void
    {
        $response = $this->get(route('module_types.show', 100));
        $response->assertStatus(404);
    }
}
