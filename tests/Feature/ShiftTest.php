<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShiftTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_gest_all_shifts_ok(): void
    {
        \App\Models\Shift::factory(10)->create();
        $response = $this->get(route('shifts.index'));

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

                ],
            ],
        ]);
    }

    public function test_get_one_shift_ok(): void
    {

        $branch = \App\Models\Branch::factory()->create();
        $room = \App\Models\Room::factory()->create([
            'branch_id' => $branch->id
        ]);
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $clientType = \App\Models\ClientType::factory()->create();
        $client = \App\Models\Client::factory()->create([
            'client_type_id' =>  $clientType->id
        ]);
        $shift = \App\Models\Shift::factory()->create([
            'room_id' => $room->id,
            'attention_profile_id' => $attentionProfile->id,
            'client_id' => $client->id,
            'state' => 'pending',
        ]);
        $response = $this->get(route('shifts.show', $shift->id));
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

    public function test_get_one_shift_not_found(): void
    {
        $response = $this->get(route('shifts.show', 10));
        $response->assertStatus(404);
    }

    public function test_create_shift_ok(): void
    {
        \Illuminate\Support\Facades\Event::fake([
            \App\Events\ShiftCreated::class,
        ]);

        $branch = \App\Models\Branch::factory()->create();
        $room = \App\Models\Room::factory()->create([
            'branch_id' => $branch->id
        ]);
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $clientType = \App\Models\ClientType::factory()->create();
        $client = \App\Models\Client::factory()->make([
            'client_type_id' =>  $clientType->id
        ])->toArray();

        $data = [
            'room_id' => $room->id,
            'attention_profile_id' => $attentionProfile->id,
            'client' => $client,
            'state' => 'pending',
        ];
        $response = $this->post(route('shifts.store'), $data);

        $response->assertStatus(201);

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


        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\ShiftCreated::class, function ($e) use ($response) {
            return $e->shift->id === $response['data']['id'];
        });
    }

    public function test_create_shift_validation_error(): void
    {
        $response = $this->post(route('shifts.store'), []);
        $response->assertStatus(422);
    }

    public function test_create_shift_room_not_found(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $clientType = \App\Models\ClientType::factory()->create();
        $client = \App\Models\Client::factory()->make([
            'client_type_id' =>  $clientType->id
        ])->toArray();

        $data = [
            'room_id' => 0,
            'attention_profile_id' => $attentionProfile->id,
            'client' => $client,
            'state' => 'pending',
        ];

        $response = $this->post(route('shifts.store'), $data);
        $response->assertStatus(422);
    }

    public function test_create_shift_attention_profile_not_found(): void
    {
        $branch = \App\Models\Branch::factory()->create();
        $room = \App\Models\Room::factory()->create([
            'branch_id' => $branch->id
        ]);
        $clientType = \App\Models\ClientType::factory()->create();
        $client = \App\Models\Client::factory()->make([
            'client_type_id' =>  $clientType->id
        ])->toArray();

        $data = [
            'room_id' => $room->id,
            'attention_profile_id' => 0,
            'client' => $client,
            'state' => 'pending',
        ];

        $response = $this->post(route('shifts.store'), $data);
        $response->assertStatus(422);
    }

    public function test_complete_shift_ok(): void
    {
        \Illuminate\Support\Facades\Event::fake([
            \App\Events\ShiftUpdated::class,
        ]);

        $branch = \App\Models\Branch::factory()->create();
        $room = \App\Models\Room::factory()->create([
            'branch_id' => $branch->id
        ]);
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $clientType = \App\Models\ClientType::factory()->create();
        $client = \App\Models\Client::factory()->create([
            'client_type_id' =>  $clientType->id
        ]);

        $shift = \App\Models\Shift::factory()->create([
            'room_id' => $room->id,
            'attention_profile_id' => $attentionProfile->id,
            'client_id' => $client->id,
            'state' => 'pending',
        ]);

        $response = $this->put(route('shifts.completed', $shift->id), []);

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

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\ShiftUpdated::class, function ($e) use ($response) {
            return $e->shift->id === $response['data']['id'];
        });
    }

    public function test_complete_shift_not_found(): void
    {
        $response = $this->put(route('shifts.completed', 10), []);
        $response->assertStatus(404);
    }

    public function test_distracted_shift_ok(): void
    {
        \Illuminate\Support\Facades\Event::fake([
            \App\Events\ShiftUpdated::class,
        ]);

        $branch = \App\Models\Branch::factory()->create();
        $room = \App\Models\Room::factory()->create([
            'branch_id' => $branch->id
        ]);
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $clientType = \App\Models\ClientType::factory()->create();
        $client = \App\Models\Client::factory()->create([
            'client_type_id' =>  $clientType->id
        ]);

        $shift = \App\Models\Shift::factory()->create([
            'room_id' => $room->id,
            'attention_profile_id' => $attentionProfile->id,
            'client_id' => $client->id,
            'state' => 'pending',
        ]);

        $response = $this->put(route('shifts.distracted', $shift->id), []);

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

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\ShiftUpdated::class, function ($e) use ($response) {
            return $e->shift->id === $response['data']['id'];
        });
    }

    public function test_distracted_shift_not_found(): void
    {
        $response = $this->put(route('shifts.distracted', 10), []);
        $response->assertStatus(404);
    }

    public function test_qualified_shift_ok(): void
    {
        \Illuminate\Support\Facades\Event::fake([
            \App\Events\ShiftUpdated::class,
        ]);

        $branch = \App\Models\Branch::factory()->create();
        $room = \App\Models\Room::factory()->create([
            'branch_id' => $branch->id
        ]);
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $clientType = \App\Models\ClientType::factory()->create();
        $client = \App\Models\Client::factory()->create([
            'client_type_id' =>  $clientType->id
        ]);

        $module = \App\Models\Module::factory()->create();

        $shift = \App\Models\Shift::factory()->create([
            'room_id' => $room->id,
            'attention_profile_id' => $attentionProfile->id,
            'client_id' => $client->id,
            'state' => 'pending',
        ]);

        \App\Models\ShiftModuleAssignation::create([
            'shift_id' => $shift->id,
            'module_id' => $module->id,
            'status' => \App\Enums\ShiftModuleAssignationState::Assigned,
        ]);

        $response = $this->put(route('shifts.qualified', $shift->id), [
            'qualification' => 4,
        ]);

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

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\ShiftUpdated::class, function ($e) use ($response) {
            return $e->shift->id === $response['data']['id'];
        });
    }
}
