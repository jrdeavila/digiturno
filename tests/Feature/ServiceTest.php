<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_all_services_ok(): void
    {
        $response = $this->get(route('services.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'service',
                ],
            ],
        ]);
    }

    public function test_get_one_service_ok(): void
    {
        $service = \App\Models\Service::factory()->create();
        $response = $this->get(route('services.show', $service->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'service',
            ],
        ]);
    }

    public function test_get_one_service_not_found(): void
    {
        $response = $this->get(route('services.show', 10));
        $response->assertStatus(404);
    }

    public function test_get_subservices_ok(): void
    {
        $service = \App\Models\Service::factory()->create();
        \App\Models\Service::factory()->count(5)->create(['service_id' => $service->id]);
        $response = $this->get(route('services.subservices.index', $service->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'service',
                ],
            ],
        ]);
    }

    public function test_create_service_ok(): void
    {
        $service = \App\Models\Service::factory()->make();
        $response = $this->post(route('services.store'), $service->toArray());
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'service',
            ],
        ]);
    }

    public function test_create_service_validation_error(): void
    {
        $service = \App\Models\Service::factory()->make(['name' => '']);
        $response = $this->post(route('services.store'), $service->toArray());
        $response->assertStatus(422);
    }

    public function test_create_service_validation_error_unique(): void
    {
        $service = \App\Models\Service::factory()->create();
        $response = $this->post(route('services.store'), $service->toArray());
        $response->assertStatus(422);
    }

    public function test_delete_service_ok(): void
    {
        $service = \App\Models\Service::factory()->create();
        $response = $this->delete(route('services.destroy', $service->id));
        $response->assertStatus(204);
    }

    public function test_delete_service_not_found(): void
    {
        $response = $this->delete(route('services.destroy', 100));
        $response->assertStatus(404);
    }

    public function test_update_service_ok(): void
    {
        $service = \App\Models\Service::factory()->create();
        $service->name = 'New Name';
        $response = $this->put(route('services.update', $service->id), $service->toArray());
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'service',
            ],
        ]);
    }

    public function test_update_service_not_found(): void
    {
        $service = \App\Models\Service::factory()->make();
        $response = $this->put(route('services.update', 100), $service->toArray());
        $response->assertStatus(404);
    }

    public function test_update_service_validation_error(): void
    {
        $service = \App\Models\Service::factory()->create();
        $service->name = '';
        $response = $this->put(route('services.update', $service->id), $service->toArray());
        $response->assertStatus(422);
    }

    public function test_update_service_validation_error_unique(): void
    {
        $service = \App\Models\Service::factory()->create();
        $service2 = \App\Models\Service::factory()->create();
        $service2->name = $service->name;
        $response = $this->put(route('services.update', $service2->id), [
            'name' => $service2->name,
        ]);
        $response->assertStatus(422);
    }
}
