<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendantAbsenceTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_all_attendant_absences_ok(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();
        $attendant->absences()->saveMany(\App\Models\AbsenceReason::factory(5)->make());
        $response = $this->get(route('attendant.absence.index', $attendant->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'attendant_id',
                    'absence_reason_id',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
        $response->assertJsonCount(5, 'data');
    }

    public function test_create_attendant_absence_ok(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();
        $absenceReason = \App\Models\AbsenceReason::factory()->create();
        $response = $this->post(
            route('attendant.absence.store', $attendant->id),
            ['absence_reason_id' => $absenceReason->id]
        );
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'attendant_id',
                'absence_reason_id',
                'created_at',
                'updated_at',
            ],
        ]);
    }
    public function test_create_attendant_absence_validation_error(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();
        $response = $this->post(
            route('attendant.absence.store', $attendant->id),
            []
        );
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'absence_reason_id',
        ]);
    }

    public function test_create_attendant_absence_not_found(): void
    {
        $response = $this->post(
            route('attendant.absence.store', 100),
            ['absence_reason_id' => 1]
        );
        $response->assertStatus(404);
    }
}
