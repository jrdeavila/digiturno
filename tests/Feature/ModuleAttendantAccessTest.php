<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ModuleAttendantAccessTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_get_all_accesses_ok(): void
    {
        \App\Models\ModuleAttendantAccess::factory(5)->create();
        $response = $this->get(route('attendant_accesses.index'));
        $response->assertStatus(200);
    }
}
