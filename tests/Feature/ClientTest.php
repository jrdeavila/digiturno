<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_all_clients_ok(): void
    {
        \App\Models\Client::factory(10)->create();
        $response = $this->get(route('clients.index'));
        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'dni',
                    'client_type',
                    'is_deleted'
                ],
            ],
        ]);
    }

    public function test_get_one_client_ok(): void
    {
        $client = \App\Models\Client::factory()->create();
        $response = $this->get(route('clients.show', $client->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'dni',
                'client_type',
                'is_deleted'
            ],
        ]);
    }

    public function test_create_client_ok(): void
    {
        $client = \App\Models\Client::factory()->make();
        $response = $this->post(route('clients.store'), $client->toArray());
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'dni',
                'client_type',
                'is_deleted'
            ],
        ]);
    }

    public function test_create_client_validation_error(): void
    {
        $response = $this->post(route('clients.store'), []);
        $response->assertStatus(422);
        $response->assertJsonStructure(
            [
                'name',
                'dni',
                'client_type_id',
            ],
        );
    }

    public function test_create_client_unique_error(): void
    {
        $client = \App\Models\Client::factory()->create();
        $response = $this->post(route('clients.store'), $client->toArray());
        $response->assertStatus(422);
        $response->assertJsonStructure(
            [
                'dni',
            ],
        );
    }

    public function test_create_client_type_not_found_error(): void
    {
        $client = \App\Models\Client::factory()->make(['client_type_id' => 999]);
        $response = $this->post(route('clients.store'), $client->toArray());
        $response->assertStatus(422);
        $response->assertJsonStructure(
            [
                'client_type_id',
            ],
        );
    }

    public function test_update_client_ok(): void
    {
        $client = \App\Models\Client::factory()->create();
        $response = $this->put(route('clients.update', $client->id), $client->toArray());
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'dni',
                'client_type',
            ],
        ]);
    }

    public function test_update_client_validation_error(): void
    {
        $client = \App\Models\Client::factory()->create();
        $response = $this->put(route('clients.update', $client->id), []);
        $response->assertStatus(422);
        $response->assertJsonStructure(
            [
                'name',
                'dni',
                'client_type_id',
            ],
        );
    }

    public function test_update_client_unique_error(): void
    {
        $client1 = \App\Models\Client::factory()->create();
        $client2 = \App\Models\Client::factory()->create();
        $response = $this->put(route('clients.update', $client1->id), ['dni' => $client2->dni]);
        $response->assertStatus(422);
        $response->assertJsonStructure(
            [
                'dni',
            ],
        );
    }

    public function test_update_client_type_not_found_error(): void
    {
        $client = \App\Models\Client::factory()->create();
        $response = $this->put(route('clients.update', $client->id), ['client_type_id' => 999]);
        $response->assertStatus(422);
        $response->assertJsonStructure(
            [
                'client_type_id',
            ],
        );
    }

    public function test_delete_client_ok(): void
    {
        $client = \App\Models\Client::factory()->create();
        $response = $this->delete(route('clients.destroy', $client->id));
        $response->assertStatus(204);
    }

    public function test_delete_client_not_found_error(): void
    {
        $response = $this->delete(route('clients.destroy', 999));
        $response->assertStatus(404);
    }
}
