<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoomShiftsTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_room_shifts_ok(): void

    {
        $room = \App\Models\Room::factory()->create();
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $room->attentionProfiles()->attach($attentionProfile->id);
        \App\Models\Shift::factory()->create([
            'room_id' => $room->id,
            'state' => \App\Enums\ShiftState::Pending,
            'attention_profile_id' => $attentionProfile->id

        ]);

        $response = $this->get(route('rooms.shifts.index', [
            'room' => $room->id,
            'attention_profile' => $attentionProfile->id
        ]));
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
                ]
            ]
        ]);
        $response->assertJsonCount(1, 'data');
    }

    public function test_get_room_shifts_room_not_found(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $response = $this->get(route('rooms.shifts.index', [
            'room' => 100,
            'attention_profile' => $attentionProfile->id
        ]));
        $response->assertStatus(404);
    }

    public function test_get_room_shifts_attention_profile_not_found(): void
    {
        $room = \App\Models\Room::factory()->create();
        $response = $this->get(route('rooms.shifts.index', [
            'room' => $room->id,
            'attention_profile' => 100
        ]));
        $response->assertStatus(404);
    }

    public function test_get_room_shifts_not_found(): void
    {
        $response = $this->get(route('rooms.shifts.index', [
            'room' => 1000,
            'attention_profile' => 10000
        ]));
        $response->assertStatus(404);
    }

    public function test_get_room_shifts_distracted_ok(): void
    {
        $room = \App\Models\Room::factory()->create();
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $room->attentionProfiles()->attach($attentionProfile->id);
        \App\Models\Shift::factory()->create([
            'room_id' => $room->id,
            'state' => \App\Enums\ShiftState::Distracted,
            'attention_profile_id' => $attentionProfile->id
        ]);

        $response = $this->get(route('rooms.shifts.distracted', [
            'room' => $room->id,
            'attention_profile' => $attentionProfile->id
        ]));
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
                ]
            ]
        ]);
        $response->assertJsonCount(1, 'data');
    }

    public function test_get_room_shifts_distracted_room_not_found(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $response = $this->get(route('rooms.shifts.distracted', [
            'room' => 100,
            'attention_profile' => $attentionProfile->id
        ]));
        $response->assertStatus(404);
    }

    public function test_get_room_shifts_distracted_attention_profile_not_found(): void
    {
        $room = \App\Models\Room::factory()->create();
        $response = $this->get(route('rooms.shifts.distracted', [
            'room' => $room->id,
            'attention_profile' => 100
        ]));
        $response->assertStatus(404);
    }

    public function test_get_room_shifts_distracted_not_found(): void
    {
        $response = $this->get(route('rooms.shifts.distracted', [
            'room' => 1000,
            'attention_profile' => 10000
        ]));
        $response->assertStatus(404);
    }

    public function test_get_room_shifts_by_room_ok(): void
    {
        $room = \App\Models\Room::factory()->create();
        \App\Models\Shift::factory()->create([
            'room_id' => $room->id,
            'state' => \App\Enums\ShiftState::Pending
        ]);

        $response = $this->get(route('rooms.shifts.by_room', [
            'room' => $room->id
        ]));
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
                ]
            ]
        ]);
        $response->assertJsonCount(1, 'data');
    }

    public function test_get_room_shifts_by_room_not_found(): void
    {
        $response = $this->get(route('rooms.shifts.by_room', [
            'room' => 1000
        ]));
        $response->assertStatus(404);
    }

    public function test_get_room_shifts_distracted_by_room_ok(): void
    {
        $room = \App\Models\Room::factory()->create();
        \App\Models\Shift::factory()->create([
            'room_id' => $room->id,
            'state' => \App\Enums\ShiftState::Distracted
        ]);

        $response = $this->get(route('rooms.shifts.distracted_by_room', [
            'room' => $room->id
        ]));
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
                ]
            ]
        ]);
        $response->assertJsonCount(1, 'data');
    }

    public function test_get_room_shifts_distracted_by_room_not_found(): void
    {
        $response = $this->get(route('rooms.shifts.distracted_by_room', [
            'room' => 1000
        ]));
        $response->assertStatus(404);
    }
}
