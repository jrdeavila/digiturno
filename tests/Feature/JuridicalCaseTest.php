<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JuridicalCaseTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_attendant_juridical_cases_ok(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();

        \App\Models\JuridicalCase::factory(4)->create([
            "attendant_id" => $attendant->id,
        ]);

        $response = $this->get(route("attendants.juridical_cases.index", ["attendant" => $attendant->id]));


        $response->assertStatus(200);

        $response->assertJsonCount(4);

        $response->assertJsonStructure([
            "*" => [
                "id",
                "subject",
                "client_id",
                "attendant_id",
                "client",
                "attendant",
            ],
        ]);
    }

    public function test_store_juridical_case_ok(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();

        $client = \App\Models\Client::factory()->create();

        $data = [
            "subject" => "Test subject",
            "client_id" => $client->id,
        ];

        $response = $this->post(route("attendants.juridical_cases.store", ["attendant" => $attendant->id]), $data);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            "id",
            "subject",
            "client_id",
            "attendant_id",
            "client",
            "attendant",
        ]);

        $this->assertDatabaseHas("juridical_cases", $data);
    }

    public function test_update_juridical_case_ok(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();

        $case = \App\Models\JuridicalCase::factory()->create([
            "attendant_id" => $attendant->id,
        ]);

        $data = [
            "subject" => "Updated subject",
            "client_id" => $case->client_id,
        ];

        $response = $this->put(route("attendants.juridical_cases.update", ["attendant" => $attendant->id, "juridical_case" => $case->id]), $data);


        $response->assertStatus(200);

        $response->assertJsonStructure([
            "id",
            "subject",
            "client_id",
            "attendant_id",
            "client",
            "attendant",
        ]);

        $this->assertDatabaseHas("juridical_cases", $data);
    }

    public function test_show_juridical_case_ok(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();

        $case = \App\Models\JuridicalCase::factory()->create([
            "attendant_id" => $attendant->id,
        ]);

        $response = $this->get(route("attendants.juridical_cases.show", ["attendant" => $attendant->id, "juridical_case" => $case->id]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "id",
            "subject",
            "client_id",
            "attendant_id",
            "client",
            "attendant",
        ]);
    }

    public function test_destroy_juridical_case_ok(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();

        $case = \App\Models\JuridicalCase::factory()->create([
            "attendant_id" => $attendant->id,
        ]);

        $response = $this->delete(route("attendants.juridical_cases.destroy", ["attendant" => $attendant->id, "juridical_case" => $case->id]));

        $response->assertStatus(204);

        $this->assertDatabaseMissing("juridical_cases", ["id" => $case->id]);
    }

    public function test_add_observation_ok(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();
        $case = \App\Models\JuridicalCase::factory()->create();

        $data = [
            'content' => fake()->randomHtml(),
            'attendant_id' => $attendant->id,
        ];

        $response = $this->post(route("attendants.juridical_cases.observations.store", [
            "juridical_case" => $case->id,
            'attendant' => $case->attendant_id
        ]), $data);


        $response->assertStatus(201);

        $response->assertJsonStructure([
            "id",
            "content",
            "juridical_case_id",
            "attendant_id",
            "juridical_case",
            "attendant",
        ]);

        $this->assertDatabaseMissing("juridical_case_observations", $data);
    }

    public function test_destroy_observation_ok(): void
    {
        $observation = \App\Models\JuridicalCaseObservation::factory()->create();

        $response = $this->delete(route("attendants.juridical_cases.observations.destroy", [
            "observation" => $observation->id,
            'attendant' => $observation->juridicalCase->attendant_id,
        ]));

        $response->assertStatus(204);
    }
}
