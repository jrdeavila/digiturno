<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShiftTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $this->withHeaders([
            'X-Module-Ip' => $module->ip_address,
        ]);
    }


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
        $services = \App\Models\Service::factory(3)->create()->pluck('id')->toArray();
        $attentionProfile->services()->attach($services);
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
            'status' => 'online',
            'attention_profile_id' => $attentionProfile->id,
            'room_id' => $room->id,
        ]);
        $clientType = \App\Models\ClientType::factory()->create();
        $client = \App\Models\Client::factory()->make([
            'client_type_id' =>  $clientType->id
        ])->toArray();

        $data = [
            'room_id' => $room->id,
            'client' => $client,
            'state' => 'pending',
            'services' => $services,
            'module_id' => $module->id,
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

    public function test_create_shift_with_attention_profile_ok(): void
    {
        \Illuminate\Support\Facades\Event::fake([
            \App\Events\ShiftCreated::class,
        ]);

        $branch = \App\Models\Branch::factory()->create();
        $room = \App\Models\Room::factory()->create([
            'branch_id' => $branch->id
        ]);
        $attentionProfile = \App\Models\AttentionProfile::factory([
            'room_id' => $room->id
        ])->create();
        $clientType = \App\Models\ClientType::factory()->create();
        $client = \App\Models\Client::factory()->make([
            'client_type_id' =>  $clientType->id
        ])->toArray();

        $modules =    \App\Models\Module::factory(4)->create([
            'enabled' => true,
            'status' => 'online',
            'attention_profile_id' => $attentionProfile->id,
            'room_id' => $room->id,
        ]);

        $modules->each(function ($module) {
            \App\Models\ModuleAttendantAccess::create([
                'module_id' => $module->id,
                'attendant_id' => \App\Models\Attendant::factory()->create()->id,
            ]);
            \App\Models\Shift::factory(5)->create([
                'module_id' => $module->id,
                'state' => 'qualified',
            ]);
        });



        $data = [
            'room_id' => $room->id,
            'attention_profile_id' => $attentionProfile->id,
            'client' => $client,
            'state' => 'pending',
        ];

        $response = $this->post(route('shifts.with-attention-profile'), $data);

        echo $response->getContent();

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

    public function test_create_shift_with_attention_profile_validation_error(): void
    {
        $response = $this->post(route('shifts.with-attention-profile'), []);
        $response->assertStatus(422);
    }

    public function test_create_shift_with_attention_profile_room_not_found(): void
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

        $response = $this->post(route('shifts.with-attention-profile'), $data);
        $response->assertStatus(422);
    }

    public function test_create_shift_with_attention_profile_attention_profile_not_found(): void
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

        $response = $this->post(route('shifts.with-attention-profile'), $data);
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

    public function test_qualified_shift_no_qualified_ok(): void
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


        $response = $this->put(route('shifts.qualified', $shift->id), [
            'qualification' => 0,
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

    public function test_qualified_shift_bad_ok(): void
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

        $response = $this->put(route('shifts.qualified', $shift->id), [
            'qualification' => 1,
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

    public function test_qualified_shift_regular_ok(): void
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

        $response = $this->put(route('shifts.qualified', $shift->id), [
            'qualification' => 2,
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

    public function test_qualified_shift_good_ok(): void
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

        $response = $this->put(route('shifts.qualified', $shift->id), [
            'qualification' => 3,
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

    public function test_qualified_shift_excellent_ok(): void
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



    public function test_qualified_shift_not_found(): void
    {
        $response = $this->put(route('shifts.qualified', 10), []);
        $response->assertStatus(404);
    }

    public function test_transfer_shift_ok(): void
    {
        $branch = \App\Models\Branch::factory()->create();
        $room = \App\Models\Room::factory()->create([
            'branch_id' => $branch->id
        ]);
        $attentionProfile = \App\Models\AttentionProfile::factory([
            'room_id' => $room->id
        ])->create();
        $clientType = \App\Models\ClientType::factory()->create();
        $client = \App\Models\Client::factory()->create([
            'client_type_id' =>  $clientType->id
        ]);

        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
            'status' => 'online',
            'attention_profile_id' => $attentionProfile->id,
            'room_id' => $room->id,
        ]);

        $shift = \App\Models\Shift::factory()->create([
            'room_id' => $room->id,
            'attention_profile_id' => $attentionProfile->id,
            'client_id' => $client->id,
            'state' => 'in_progress',
            'module_id' => $module->id,
        ]);

        $attentionProfile2 = \App\Models\AttentionProfile::factory()->create();

        $modules =   \App\Models\Module::factory(4)->create([
            'enabled' => true,
            'status' => 'online',
            'attention_profile_id' => $attentionProfile2->id,
            'room_id' => $room->id,
        ]);

        $modules->each(function ($module) {
            \App\Models\ModuleAttendantAccess::create([
                'module_id' => $module->id,
                'attendant_id' => \App\Models\Attendant::factory()->create()->id,
            ]);
            \App\Models\Shift::factory(5)->create([
                'module_id' => $module->id,
                'state' => 'qualified',
            ]);
        });

        $response = $this->put(route('shifts.transfer', $shift->id), [
            'qualification' => 4,
            'attention_profile_id' => $attentionProfile2->id,
        ]);


        echo $response->getContent();


        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'room',
                'attention_profile',
                'client',
                'state',
                'module_id',
                'created_at',
                'updated_at',
            ],
        ]);
        $this->assertNotEquals(
            $shift->id,
            $response['data']['id']
        );

        $this->assertNotEquals(
            $module->id,
            $response['data']['module_id']
        );
    }

    public function test_transfer_shift_not_found(): void
    {
        $response = $this->put(route('shifts.transfer', 10), []);
        $response->assertStatus(404);
    }

    public function test_send_to_pending_ok(): void
    {
        \Illuminate\Support\Facades\Event::fake([
            \App\Events\ShiftUpdated::class,
        ]);

        $shift = \App\Models\Shift::factory()->create([
            'state' => 'distracted',
        ]);
        $response = $this->put(route('shifts.pending', $shift->id), []);
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

    public function test_send_to_pending_not_found(): void
    {
        $response = $this->put(route('shifts.pending', 10), []);
        $response->assertStatus(404);
    }

    public function test_call_shift_ok(): void
    {


        \Illuminate\Support\Facades\Event::fake([
            \App\Events\CallClient::class,
        ]);

        $shift = \App\Models\Shift::factory()->create([
            'state' => 'pending',
        ]);
        $response = $this->put(route('shifts.call', $shift->id), []);

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\CallClient::class, function ($e) use ($shift) {
            return $e->shift->id === $shift->id;
        });

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

    public function test_call_shift_not_found(): void
    {
        $response = $this->put(route('shifts.call', 10), []);
        $response->assertStatus(404);
    }

    public function test_send_to_in_progress_ok(): void
    {
        \Illuminate\Support\Facades\Event::fake([
            \App\Events\ShiftUpdated::class,
        ]);

        $shift = \App\Models\Shift::factory()->create([
            'state' => 'pending',
            'module_id' => null,
        ]);
        $attendant = \App\Models\Attendant::factory()->create();
        $module = \App\Models\Module::factory()->create();
        \App\Models\ModuleAttendantAccess::create([
            'module_id' => $module->id,
            'attendant_id' => $attendant->id,
        ]);
        $response = $this->put(route('shifts.in-progress', $shift->id), [
            'module_id' => $module->id,
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

    public function test_send_to_in_progress_shift_when_module_is_busy(): void
    {
        $shift = \App\Models\Shift::factory()->create([
            'state' => \App\Enums\ShiftState::Pending,
        ]);
        $module = \App\Models\Module::factory()->create();
        \App\Models\Shift::factory()->create([
            'state' => \App\Enums\ShiftState::InProgress,
            'module_id' => $module->id,
        ]);
        $response = $this->put(route('shifts.in-progress', $shift->id), [
            'module_id' => $module->id,
        ]);
        $response->assertStatus(400);
    }

    public function test_send_to_in_progress_shift_when_module_is_qualifying(): void
    {
        $shift = \App\Models\Shift::factory()->create([
            'state' => \App\Enums\ShiftState::Completed,
        ]);
        $module = \App\Models\Module::factory()->create();
        \App\Models\Shift::factory()->create([
            'state' => \App\Enums\ShiftState::InProgress,
            'module_id' => $module->id,
        ]);
        $response = $this->put(route('shifts.in-progress', $shift->id), [
            'module_id' => $module->id,
        ]);
        $response->assertStatus(400);
    }



    public function test_send_to_in_progress_validation_error(): void
    {
        $shift = \App\Models\Shift::factory()->create([
            'state' => \App\Enums\ShiftState::Pending,
        ]);
        $response = $this->put(route('shifts.in-progress', $shift->id), []);
        $response->assertStatus(422);
    }

    public function test_send_to_in_progress_not_found(): void
    {
        $response = $this->put(route('shifts.in-progress', 10), []);
        $response->assertStatus(404);
    }

    public function test_delete_shift_ok(): void
    {
        \Illuminate\Support\Facades\Event::fake([
            \App\Events\ShiftDeleted::class,
        ]);
        $shift = \App\Models\Shift::factory()->create();
        $response = $this->delete(route('shifts.destroy', $shift->id));
        $response->assertStatus(204);
        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\ShiftDeleted::class);
    }

    public function test_delete_shift_not_found(): void
    {
        $response = $this->delete(route('shifts.destroy', 10));
        $response->assertStatus(404);
    }
}
