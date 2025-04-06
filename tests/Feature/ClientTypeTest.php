<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientTypeTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_all_client_type_ok(): void
    {
        $response = $this->get(route('client_types.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'slug',
                    'priority'
                ],
            ],
        ]);
    }


    public function test_get_one_client_type_ok(): void
    {

        $clientType = \App\Models\ClientType::factory()->create();

        $response = $this->get(route('client_types.show', $clientType->id));
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'priority',
            ],
        ]);
    }

    public function test_get_one_client_type_not_found(): void
    {
        $response = $this->get(route('client_types.show', 100));
        $response->assertStatus(404);
    }

    public function test_create_client_type_ok(): void
    {

        $data = \App\Models\ClientType::factory()->make()->toArray();
        $response = $this->post(route('client_types.store'), $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'priority',
            ],
        ]);
    }

    public function test_create_client_type_validation_error(): void
    {
        $data = [];
        $response = $this->post(route('client_types.store'), $data);
        $response->assertStatus(422);
    }

    public function test_create_client_type_unique_error(): void
    {
        $clientType = \App\Models\ClientType::factory()->create();
        $data = $clientType->toArray();
        $response = $this->post(route('client_types.store'), $data);
        $response->assertStatus(422);
    }

    public function test_create_client_type_slug_unique_error(): void
    {
        $clientType = \App\Models\ClientType::factory()->create();
        $data = \App\Models\ClientType::factory()->make(['slug' => $clientType->slug])->toArray();
        $response = $this->post(route('client_types.store'), $data);
        $response->assertStatus(422);
    }

    public function test_update_client_type_ok(): void
    {
        $clientType = \App\Models\ClientType::factory()->create();
        $data = \App\Models\ClientType::factory()->make()->toArray();
        $response = $this->put(route('client_types.update', $clientType->id), $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'slug',
                'priority',
            ],
        ]);
    }

    public function test_update_client_type_validation_error(): void
    {
        $clientType = \App\Models\ClientType::factory()->create();
        $data = [];
        $response = $this->put(route('client_types.update', $clientType->id), $data);
        $response->assertStatus(422);
    }

    public function test_update_client_type_unique_error(): void
    {
        $clientType = \App\Models\ClientType::factory()->create();
        $clientType2 = \App\Models\ClientType::factory()->create();
        $data = $clientType2->toArray();
        $response = $this->put(route('client_types.update', $clientType->id), $data);
        $response->assertStatus(422);
    }

    public function test_update_client_type_slug_unique_error(): void
    {
        $clientType = \App\Models\ClientType::factory()->create([
            'slug' => 'slug',
        ]);

        $clientType2 = \App\Models\ClientType::factory()->create([
            'slug' => 'slug2',
        ]);

        $data = $clientType->toArray();
        $data['slug'] = $clientType->slug;
        $response = $this->put(route('client_types.update', $clientType2->id), $data);
        $response->assertStatus(422);
    }

    public function test_delete_client_type_ok(): void
    {
        $clientType = \App\Models\ClientType::factory()->create();
        $response = $this->delete(route('client_types.destroy', $clientType->id));
        $response->assertStatus(204);
    }

    public function test_delete_client_type_not_found(): void
    {
        $response = $this->delete(route('client_types.destroy', 100));
        $response->assertStatus(404);
    }
}
