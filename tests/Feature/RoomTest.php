<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoomTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_all_room_ok(): void
    {
        $response = $this->get(route('rooms.index'));
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                ],
            ],
        ]);
    }

    public function test_get_one_room_ok(): void
    {

        \App\Models\Room::factory()->create();


        $response = $this->get(route('rooms.show', 1));
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
            ],
        ]);
    }


    public function test_get_one_room_not_found(): void
    {
        $response = $this->get(route('rooms.show', 1));
        $response->assertStatus(404);
    }


    public function test_create_room_ok(): void
    {
        $response = $this->post(route('rooms.store'), [
            'name' => 'Sala 1'
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
            ],
        ]);
    }

    public function test_create_room_validation_error(): void
    {
        $response = $this->post(route('rooms.store'), []);
        $response->assertStatus(422);
    }

    public function test_create_room_unique_error(): void
    {
        $room = \App\Models\Room::factory()->create();
        $data = $room->toArray();

        $response = $this->post(route('rooms.store'), $data);

        $response->assertStatus(422);
    }


    public function test_update_room_ok(): void
    {
        $room = \App\Models\Room::factory()->create();
        $data = \App\Models\Room::factory()->make()->toArray();

        $response = $this->put(route('rooms.update', $room->id), $data);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
            ],
        ]);
    }

    public function test_update_room_validation_error(): void
    {
        $room = \App\Models\Room::factory()->create();
        $response = $this->put(route('rooms.update', $room->id), []);
        $response->assertStatus(422);
    }

    public function test_update_room_not_found(): void
    {
        $data = \App\Models\Room::factory()->make()->toArray();
        $response = $this->put(route('rooms.update', 1), $data);

        $response->assertStatus(404);
    }

    public function test_update_room_unique_error(): void
    {
        $room = \App\Models\Room::factory()->create();
        $room2 = \App\Models\Room::factory()->create();
        $data = $room2->toArray();

        $response = $this->put(route('rooms.update', $room->id), $data);

        $response->assertStatus(422);
    }


    public function test_delete_room_ok(): void
    {
        $room =  \App\Models\Room::factory()->create();

        $response = $this->delete(route('rooms.destroy', $room->id));
        $response->assertStatus(204);
    }

    public function test_delete_room_not_found(): void
    {
        $response = $this->delete(route('rooms.destroy', 1));
        $response->assertStatus(404);
    }
}
