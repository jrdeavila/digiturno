<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModuleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_all_modules_ok(): void
    {
        \App\Models\Module::factory(5)->create();
        $response = $this->get(route('modules.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'ip_address',
                    'room',
                    'type',
                    'enabled',
                    'status',
                    'attention_profile_id'
                ],
            ],
        ]);
        $response->assertJsonCount(5, 'data');
    }

    public function test_get_only_one_module_ok(): void
    {
        $module = \App\Models\Module::factory()->create();
        $response = $this->get(route('modules.show', $module));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'ip_address',
                'room',
                'type',
                'enabled',
                'status',
                'attention_profile_id',
            ],
        ]);
    }

    public function test_get_only_one_module_not_found(): void
    {
        $response = $this->get(route('modules.show', 100));
        $response->assertStatus(404);
    }

    public function test_create_module_ok(): void
    {
        $module = \App\Models\Module::factory()->make();
        $response = $this->post(route('modules.store'), $module->toArray());
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'ip_address',
                'room',
                'type',
                'enabled',
                'status',
                'attention_profile_id',
            ],
        ]);
    }

    public function test_create_module_validation_error_name_empty(): void
    {
        $module = \App\Models\Module::factory()->make(['name' => '']);
        $response = $this->post(route('modules.store'), $module->toArray());
        $response->assertStatus(422);
    }

    public function test_create_module_validation_error_ip_address_empty(): void
    {
        $module = \App\Models\Module::factory()->make(['ip_address' => '']);
        $response = $this->post(route('modules.store'), $module->toArray());
        $response->assertStatus(422);
    }

    public function test_create_module_validation_error_ip_address_invalid(): void
    {
        $module = \App\Models\Module::factory()->make(['ip_address' => 'another_ip']);
        $response = $this->post(route('modules.store'), $module->toArray());
        $response->assertStatus(422);
    }

    public function test_create_module_validation_error_room_id_empty(): void
    {
        $module = \App\Models\Module::factory()->make(['room_id' => '']);
        $response = $this->post(route('modules.store'), $module->toArray());
        $response->assertStatus(422);
    }

    public function test_create_module_validation_error_room_id_not_exists(): void
    {
        $module = \App\Models\Module::factory()->make(['room_id' => 100]);
        $response = $this->post(route('modules.store'), $module->toArray());
        $response->assertStatus(422);
    }

    public function test_create_module_validation_error_client_type_id_empty(): void
    {
        $module = \App\Models\Module::factory()->make(['client_type_id' => '']);
        $response = $this->post(route('modules.store'), $module->toArray());
        $response->assertStatus(422);
    }

    public function test_create_module_validation_error_client_type_id_not_exists(): void
    {
        $module = \App\Models\Module::factory()->make(['client_type_id' => 100]);
        $response = $this->post(route('modules.store'), $module->toArray());
        $response->assertStatus(422);
    }

    public function test_update_module_ok(): void
    {
        $module = \App\Models\Module::factory()->create();
        $module->name = 'New Name';
        $response = $this->put(route('modules.update', $module), $module->toArray());
        $response->assertStatus(200);
    }

    public function test_update_module_validation_error_name_empty(): void
    {
        $module = \App\Models\Module::factory()->create();
        $module->name = '';
        $response = $this->put(route('modules.update', $module), $module->toArray());
        $response->assertStatus(422);
    }

    public function test_update_module_validation_error_ip_address_empty(): void
    {
        $module = \App\Models\Module::factory()->create();
        $module->ip_address = '';
        $response = $this->put(route('modules.update', $module), $module->toArray());
        $response->assertStatus(422);
    }

    public function test_update_module_validation_error_ip_address_invalid(): void
    {
        $module = \App\Models\Module::factory()->create();
        $module->ip_address = 'another_ip';
        $response = $this->put(route('modules.update', $module), $module->toArray());
        $response->assertStatus(422);
    }

    public function test_update_module_validation_error_room_id_empty(): void
    {
        $module = \App\Models\Module::factory()->create();
        $module->room_id = '';
        $response = $this->put(route('modules.update', $module), $module->toArray());
        $response->assertStatus(422);
    }

    public function test_update_module_validation_error_room_id_not_exists(): void
    {
        $module = \App\Models\Module::factory()->create();
        $module->room_id = 100;
        $response = $this->put(route('modules.update', $module), $module->toArray());
        $response->assertStatus(422);
    }

    public function test_update_module_validation_error_client_type_id_empty(): void
    {
        $module = \App\Models\Module::factory()->create();
        $module->client_type_id = '';
        $response = $this->put(route('modules.update', $module), $module->toArray());
        $response->assertStatus(422);
    }

    public function test_update_module_validation_error_client_type_id_not_exists(): void
    {
        $module = \App\Models\Module::factory()->create();
        $module->client_type_id = 100;
        $response = $this->put(route('modules.update', $module), $module->toArray());
        $response->assertStatus(422);
    }

    public function test_delete_module_ok(): void
    {
        $module = \App\Models\Module::factory()->create();
        $response = $this->delete(route('modules.destroy', $module));
        $response->assertStatus(204);
    }

    public function test_delete_module_not_found(): void
    {
        $response = $this->delete(route('modules.destroy', 100));
        $response->assertStatus(404);
    }
}
