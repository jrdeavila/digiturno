<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttentionProfileTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_all_attention_profile_test_ok(): void
    {
        \App\Models\AttentionProfile::factory(5)->create()->each(function ($ap) {
            $ap->services()->attach(\App\Models\Service::factory(5)->create());
        });
        $response = $this->get(route('attention_profiles.index'));
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'services' => [
                        '*' => [
                            'id',
                            'name',
                        ],
                    ],
                ],
            ],
        ]);
    }


    public function test_get_one_attention_profile_test_ok(): void
    {

        $ap = \App\Models\AttentionProfile::factory()->create();
        $ap->services()->attach(\App\Models\Service::factory(5)->create());

        $response = $this->get(route('attention_profiles.show', $ap->id));
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'services' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ],
        ]);
    }

    public function test_get_one_attention_profile_test_not_found(): void
    {
        $response = $this->get(route('attention_profiles.show', 100));
        $response->assertStatus(404);
    }

    public function test_create_attention_profile_test_ok(): void
    {

        $service = \App\Models\Service::factory()->create();
        $ap = \App\Models\AttentionProfile::factory()->make();

        $response = $this->post(route('attention_profiles.store'), [
            'name' => $ap->name,
            'services' => [
                $service->id,
            ],
        ]);


        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
            ],
        ]);
    }

    public function test_create_attention_profile_test_validation_error(): void
    {
        $data = [
            'name' => '',
        ];

        $response = $this->post(route('attention_profiles.store'), $data);

        $response->assertStatus(422);
    }

    public function test_create_attention_profile_test_unique_error(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();

        $data = [
            'name' => $attentionProfile->name,
        ];

        $response = $this->post(route('attention_profiles.store'), $data);

        $response->assertStatus(422);
    }

    public function test_create_attention_profile_test_name_required_error(): void
    {
        $data = [
            'name' => '',
        ];

        $response = $this->post(route('attention_profiles.store'), $data);

        $response->assertStatus(422);
    }



    public function test_update_attention_profile_test_ok(): void
    {
        $service = \App\Models\Service::factory()->create();
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();



        $response = $this->put(route('attention_profiles.update', $attentionProfile->id), [
            'name' => 'name',
            'services' => [
                $service->id,
            ],
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
            ],
        ]);
    }

    public function test_update_attention_profile_test_validation_error(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();

        $data = [
            'name' => '',
        ];

        $response = $this->put(route('attention_profiles.update', $attentionProfile->id), $data);

        $response->assertStatus(422);
    }

    public function test_update_attention_profile_test_unique_error(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $attentionProfile2 = \App\Models\AttentionProfile::factory()->create();

        $data = [
            'name' => $attentionProfile2->name,
        ];

        $response = $this->put(route('attention_profiles.update', $attentionProfile->id), $data);

        $response->assertStatus(422);
    }

    public function test_update_attention_profile_test_name_required_error(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();

        $data = [
            'name' => '',
            'description' => 'description',
        ];

        $response = $this->put(route('attention_profiles.update', $attentionProfile->id), $data);

        $response->assertStatus(422);
    }


    public function test_delete_attention_profile_test_ok(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();

        $response = $this->delete(route('attention_profiles.destroy', $attentionProfile->id));

        $response->assertStatus(204);
    }

    public function test_delete_attention_profile_test_not_found(): void
    {
        $response = $this->delete(route('attention_profiles.destroy', 100));

        $response->assertStatus(404);
    }

    public function test_get_all_attention_profile_services_test_ok(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $services = \App\Models\Service::factory(5)->create();

        $attentionProfile->services()->attach($services);

        $response = $this->get(route('attention_profiles.services.index', $attentionProfile->id));
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

    public function test_get_all_attention_profile_services_test_not_found(): void
    {
        $response = $this->get(route('attention_profiles.services.index', 100));
        $response->assertStatus(404);
    }

    public function test_store_attention_profile_services_test_ok(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $service = \App\Models\Service::factory()->create();

        $data = [
            'service_id' => $service->id,
        ];

        $response = $this->post(route('attention_profiles.services.store', $attentionProfile->id), $data);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'service',
            ],
        ]);
    }

    public function test_store_attention_profile_services_test_validation_error(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();

        $data = [
            'service_id' => '',
        ];

        $response = $this->post(route('attention_profiles.services.store', $attentionProfile->id), $data);

        $response->assertStatus(422);
    }

    public function test_store_attention_profile_services_test_not_found(): void
    {
        $data = [
            'service_id' => 1,
        ];

        $response = $this->post(route('attention_profiles.services.store', 100), $data);

        $response->assertStatus(404);
    }

    public function test_destroy_attention_profile_services_test_ok(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();
        $service = \App\Models\Service::factory()->create();

        $attentionProfile->services()->attach($service);

        $response = $this->delete(route('attention_profiles.services.destroy', [$attentionProfile->id, $service->id]));

        $response->assertStatus(204);
    }

    public function test_destroy_attention_profile_services_test_not_found(): void
    {
        $response = $this->delete(route('attention_profiles.services.destroy', [100, 1]));

        $response->assertStatus(404);
    }

    public function test_destroy_attention_profile_services_test_not_found_service(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();

        $response = $this->delete(route('attention_profiles.services.destroy', [$attentionProfile->id, 100]));

        $response->assertStatus(404);
    }
}
