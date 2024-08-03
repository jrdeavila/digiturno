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
        \Illuminate\Support\Facades\Bus::fake([
            \App\Jobs\AttendantLogin::class
        ]);
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $attendant = \App\Models\Attendant::factory()->create();
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.login'), [
                'email' => $attendant->email,
                'password' => $attendant->dni,
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token']);

        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\AttendantLogin::class, function ($job) use ($attendant) {
            return $job->attendant->is($attendant);
        });
    }

    public function test_login_attendant_disabled_attendant(): void
    {
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $attendant = \App\Models\Attendant::factory()->create([
            'enabled' => false,
        ]);
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.login'), [
                'email' => $attendant->email,
                'password' => $attendant->dni,
            ]);

        $response->assertStatus(403);

        $response->assertJsonStructure(['message', 'help']);
    }

    public function test_login_attendant_disabled_module(): void
    {
        $module = \App\Models\Module::factory()->create([
            'enabled' => false,
        ]);
        $attendant = \App\Models\Attendant::factory()->create();
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.login'), [
                'email' => $attendant->email,
                'password' => $attendant->dni,
            ]);

        $response->assertStatus(403);

        $response->assertJsonStructure(['message', 'help']);
    }

    public function test_login_attendant_invalid_module_ip(): void
    {
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $attendant = \App\Models\Attendant::factory()->create();
        $response = $this
            ->withHeader('X-Module-Ip', 'invalid')
            ->post(route('attendants.login'), [
                'email' => $attendant->email,
                'password' => $attendant->dni,
            ]);

        $response->assertStatus(400);

        $response->assertJsonStructure(['message', 'help']);
    }

    public function test_login_attendant_no_module_ip(): void
    {
        $attendant = \App\Models\Attendant::factory()->create();
        $response = $this->post(route('attendants.login'), [
            'email' => $attendant->email,
            'password' => $attendant->dni,
        ]);

        $response->assertStatus(400);

        $response->assertJsonStructure(['message', 'help']);
    }



    public function test_login_attendant_invalid_credentials(): void
    {
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $attendant = \App\Models\Attendant::factory()->create();
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.login', [
                'email' => $attendant->email,
                'password' => 'invalid',
            ]));

        $response->assertStatus(401);

        $response->assertJsonStructure(['message', 'help']);
    }

    public function test_login_attendant_missing_email(): void
    {
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $attendant = \App\Models\Attendant::factory()->create();
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.login', [
                'password' => $attendant->dni,
            ]));

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'email',
        ]);
    }

    public function test_login_attendant_missing_password(): void
    {
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $attendant = \App\Models\Attendant::factory()->create();
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.login', [
                'email' => $attendant->email,
            ]));

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'password'
        ]);
    }

    public function test_login_attendant_missing_email_and_password(): void
    {
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.login'));

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'email',
            'password'
        ]);
    }

    public function test_login_attendant_invalid_email(): void
    {
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $attendant = \App\Models\Attendant::factory()->create();
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.login', [
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
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.login', [
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
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.login', [
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
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $attendant = \App\Models\Attendant::factory()->create();
        $token = auth('attendant')->login($attendant);
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->get(route('attendants.profile'), [
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
        \Illuminate\Support\Facades\Bus::fake([
            \App\Jobs\ModuleOffline::class
        ]);
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->get(route('attendants.profile'));

        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\ModuleOffline::class);

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_get_attendant_profile_invalid_token(): void
    {
        \Illuminate\Support\Facades\Bus::fake([
            \App\Jobs\ModuleOffline::class
        ]);

        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);

        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->get(route('attendants.profile'), [
                'Authorization' => 'Bearer invalid',
            ]);

        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\ModuleOffline::class);

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_get_attendant_profile_missing_token(): void
    {
        \Illuminate\Support\Facades\Bus::fake([
            \App\Jobs\ModuleOffline::class
        ]);
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->get(route('attendants.profile'));

        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\ModuleOffline::class);

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_logout_attendant_ok(): void
    {
        \Illuminate\Support\Facades\Bus::fake([
            \App\Jobs\AttendantLogout::class
        ]);
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $attendant = \App\Models\Attendant::factory()->create();
        $token = auth('attendant')->login($attendant);
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.logout'), [], [
                'Authorization' => "Bearer $token",
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
        ]);

        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\AttendantLogout::class, function ($job) use ($attendant) {
            return $job->attendant->is($attendant);
        });
    }

    public function test_logout_attendant_unauthorized(): void
    {
        \Illuminate\Support\Facades\Bus::fake([
            \App\Jobs\ModuleOffline::class
        ]);
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.logout'));

        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\ModuleOffline::class);

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_logout_attendant_invalid_token(): void
    {
        \Illuminate\Support\Facades\Bus::fake([
            \App\Jobs\ModuleOffline::class
        ]);
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.logout'), [], [
                'Authorization' => 'Bearer invalid',
            ]);

        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\ModuleOffline::class);

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_logout_attendant_missing_token(): void
    {
        \Illuminate\Support\Facades\Bus::fake([
            \App\Jobs\ModuleOffline::class
        ]);
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.logout'));

        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\ModuleOffline::class);
        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_refresh_attendant_token_ok(): void
    {
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $attendant = \App\Models\Attendant::factory()->create();
        $token = auth('attendant')->login($attendant);
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.refresh'), [], [
                'Authorization' => "Bearer $token",
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
        ]);
    }

    public function test_refresh_attendant_token_unauthorized(): void
    {
        \Illuminate\Support\Facades\Bus::fake([
            \App\Jobs\ModuleOffline::class
        ]);
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.refresh'));

        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\ModuleOffline::class);

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_refresh_attendant_token_invalid_token(): void
    {
        \Illuminate\Support\Facades\Bus::fake([
            \App\Jobs\ModuleOffline::class
        ]);
        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.refresh'), [], [
                'Authorization' => 'Bearer invalid',
            ]);

        $response->assertStatus(401);

        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\ModuleOffline::class);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }

    public function test_refresh_attendant_token_missing_token(): void
    {
        \Illuminate\Support\Facades\Bus::fake([
            \App\Jobs\ModuleOffline::class
        ]);

        $module = \App\Models\Module::factory()->create([
            'enabled' => true,
        ]);
        $response = $this
            ->withHeader('X-Module-Ip', $module->ip_address)
            ->post(route('attendants.refresh'));

        \Illuminate\Support\Facades\Bus::assertDispatched(\App\Jobs\ModuleOffline::class);

        $response->assertStatus(401);

        $response->assertJsonStructure([
            'message',
            'help',
        ]);
    }
}
