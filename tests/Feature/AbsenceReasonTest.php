<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AbsenceReasonTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_all_absence_reason_ok(): void
    {
        \App\Models\AbsenceReason::factory(5)->create();
        $response = $this->get(route('absence_reason.index'));
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

    public function test_get_one_absence_reason_ok(): void
    {
        $absenceReason = \App\Models\AbsenceReason::factory()->create();
        $response = $this->get(route('absence_reason.show', $absenceReason->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
            ],
        ]);
    }

    public function test_get_one_absence_reason_not_found(): void
    {
        $response = $this->get(route('absence_reason.show', 100));
        $response->assertStatus(404);
    }

    public function test_create_absence_reason_ok(): void
    {
        $absenceReason = \App\Models\AbsenceReason::factory()->make();
        $response = $this->post(
            route('absence_reason.store'),
            $absenceReason->toArray()
        );
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
            ],
        ]);
    }

    public function test_create_absence_reason_validation_error(): void
    {
        $response = $this->post(route('absence_reason.store'), []);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'name',
        ]);
    }

    public function test_update_absence_reason_ok(): void
    {
        $absenceReason = \App\Models\AbsenceReason::factory()->create();
        $response = $this->put(route('absence_reason.update', $absenceReason->id), ['name' => 'New Name']);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
            ],
        ]);
    }


    public function test_update_absence_reason_validation_error(): void
    {
        $absenceReason = \App\Models\AbsenceReason::factory()->create();
        $response = $this->put(route('absence_reason.update', $absenceReason->id), ['name' => '']);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'name',
        ]);
    }

    public function test_update_absence_reason_not_found(): void
    {
        $response = $this->put(route('absence_reason.update', 100), ['name' => 'New Name']);
        $response->assertStatus(404);
    }

    public function test_delete_absence_reason_ok(): void
    {
        $absenceReason = \App\Models\AbsenceReason::factory()->create();
        $response = $this->delete(route('absence_reason.destroy', $absenceReason->id));
        $response->assertStatus(204);
    }

    public function test_delete_absence_reason_not_found(): void
    {
        $response = $this->delete(route('absence_reason.destroy', 100));
        $response->assertStatus(404);
    }
}
