<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendantAuthenticationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_login_attendant_ok(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();
        $response = $this->post(route('attendants.login', [
            'email' => $attendant->email,
            'password' => $attendant->dni,
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);
    }


    public function test_login_attendant_invalid_credentials(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();
        $response = $this->post(route('attendants.login', [
            'email' => $attendant->email,
            'password' => 'invalid',
        ]));

        $response->assertStatus(401);

        $response->assertJsonStructure(['message', 'help']);
    }

    public function test_login_attendant_missing_email(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();
        $response = $this->post(route('attendants.login', [
            'password' => $attendant->dni,
        ]));

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'email',
        ]);
    }

    public function test_login_attendant_missing_password(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();
        $response = $this->post(route('attendants.login', [
            'email' => $attendant->email,
        ]));

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'password'
        ]);
    }

    public function test_login_attendant_missing_email_and_password(): void
    {
        $response = $this->post(route('attendants.login'));

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'email',
            'password'
        ]);
    }

    public function test_login_attendant_invalid_email(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();
        $response = $this->post(route('attendants.login', [
            'email' => 'invalid',
            'password' => $attendant->dni,
        ]));

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'email'
        ]);
    }

    public function test_login_attendant_invalid_email_and_password(): void
    {
        $response = $this->post(route('attendants.login', [
            'email' => 'invalid',
            'password' => 'invalid',
        ]));

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'email',
        ]);
    }

    public function test_login_attendant_invalid_email_and_missing_password(): void
    {
        $response = $this->post(route('attendants.login', [
            'email' => 'invalid',
        ]));

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'email',
            'password'
        ]);
    }

    public function test_get_attendant_profile_ok(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();
        $token = auth('attendant')->login($attendant);
        $response = $this->get(route('attendants.profile'), [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'dni',
                'enabled',
            ]
        ]);
    }

    public function test_get_attendant_profile_unauthorized(): void
    {
        $response = $this->get(route('attendants.profile'));

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_get_attendant_profile_invalid_token(): void
    {
        $response = $this->get(route('attendants.profile'), [
            'Authorization' => 'Bearer invalid',
        ]);

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_get_attendant_profile_missing_token(): void
    {
        $response = $this->get(route('attendants.profile'));

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_logout_attendant_ok(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();
        $token = auth('attendant')->login($attendant);
        $response = $this->post(route('attendants.logout'), [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
        ]);
    }

    public function test_logout_attendant_unauthorized(): void
    {
        $response = $this->post(route('attendants.logout'));

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_logout_attendant_invalid_token(): void
    {
        $response = $this->post(route('attendants.logout'), [], [
            'Authorization' => 'Bearer invalid',
        ]);

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_logout_attendant_missing_token(): void
    {
        $response = $this->post(route('attendants.logout'));

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_refresh_attendant_token_ok(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();
        $token = auth('attendant')->login($attendant);
        $response = $this->post(route('attendants.refresh'), [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
        ]);
    }

    public function test_refresh_attendant_token_unauthorized(): void
    {
        $response = $this->post(route('attendants.refresh'));

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_refresh_attendant_token_invalid_token(): void
    {
        $response = $this->post(route('attendants.refresh'), [], [
            'Authorization' => 'Bearer invalid',
        ]);

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_refresh_attendant_token_missing_token(): void
    {
        $response = $this->post(route('attendants.refresh'));

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }
}
