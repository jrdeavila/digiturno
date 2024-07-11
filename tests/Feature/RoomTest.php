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
                    'branch_id'
                ],
            ],
        ]);
    }

    public function test_get_one_room_ok(): void
    {

        $branch = \App\Models\Branch::factory()->create();
        $room = \App\Models\Room::factory()->create([
            'branch_id' => $branch->id,
        ]);


        $response = $this->get(route('rooms.show', $room->id));
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'branch_id'
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
        $branch = \App\Models\Branch::factory()->create();

        $response = $this->post(route('rooms.store'), [
            'name' => 'Sala 1',
            'branch_id' => $branch->id,
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'branch_id'
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
        $branch = \App\Models\Branch::factory()->create();
        $room = \App\Models\Room::factory()->create([
            'branch_id' => $branch->id,
        ]);
        $data = $room->toArray();

        $response = $this->post(route('rooms.store'), $data);

        $response->assertStatus(422);
    }

    public function test_create_room_branch_not_found(): void
    {
        $response = $this->post(route('rooms.store'), [
            'name' => 'Sala 1',
            'branch_id' => 1,
        ]);

        $response->assertStatus(422);
    }


    public function test_update_room_ok(): void
    {
        $branch = \App\Models\Branch::factory()->create();
        $room = \App\Models\Room::factory()->create([
            'branch_id' => $branch->id,
        ]);
        $data = \App\Models\Room::factory()->make([
            'branch_id' => $branch->id,
        ])->toArray();

        $response = $this->put(route('rooms.update', $room->id), $data);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'branch_id'
            ],
        ]);
    }

    public function test_update_room_validation_error(): void
    {
        $branch = \App\Models\Branch::factory()->create();
        $room = \App\Models\Room::factory()->create([
            'branch_id' => $branch->id,
        ]);
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
        $branch = \App\Models\Branch::factory()->create();
        $room = \App\Models\Room::factory()->create([
            'branch_id' => $branch->id,
        ]);
        $room2 = \App\Models\Room::factory()->create([
            'branch_id' => $branch->id,
        ]);
        $data = $room2->toArray();

        $response = $this->put(route('rooms.update', $room->id), $data);

        $response->assertStatus(422);
    }


    public function test_delete_room_ok(): void
    {
        $branch = \App\Models\Branch::factory()->create();
        $room = $branch->rooms()->save(\App\Models\Room::factory()->make());

        $response = $this->delete(route('rooms.destroy', $room->id));
        $response->assertStatus(204);
    }

    public function test_delete_room_not_found(): void
    {
        $response = $this->delete(route('rooms.destroy', 1));
        $response->assertStatus(404);
    }
}
