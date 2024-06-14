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
        $response = $this->get(route('attention_profiles.index'));
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'attention_profiles',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_get_one_attention_profile_with_attention_profiles_test_ok(): void
    {
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();

        \App\Models\AttentionProfile::factory()->create([
            'attention_profile_id' => $attentionProfile->id,
        ]);
        $response = $this->get(route('attention_profiles.show', $attentionProfile->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'attention_profiles',
            ],
        ]);
    }



    public function test_get_one_attention_profile_test_ok(): void
    {

        $ap = \App\Models\AttentionProfile::factory()->create();

        $response = $this->get(route('attention_profiles.show', $ap->id));
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
            ],
        ]);
    }

    public function test_get_one_attention_profile_test_not_found(): void
    {
        $response = $this->get(route('attention_profiles.show', 1));
        $response->assertStatus(404);
    }

    public function test_create_attention_profile_test_ok(): void
    {

        $data = \App\Models\AttentionProfile::factory()->make()->toArray();

        $response = $this->post(route('attention_profiles.store'), $data);

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
        $attentionProfile = \App\Models\AttentionProfile::factory()->create();

        $data = [
            'name' => 'name',
        ];

        $response = $this->put(route('attention_profiles.update', $attentionProfile->id), $data);

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
        $response = $this->delete(route('attention_profiles.destroy', 1));

        $response->assertStatus(404);
    }
}
