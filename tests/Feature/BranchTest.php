<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BranchTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_get_all_branches_ok(): void
    {
        \App\Models\Branch::factory()->count(5)->create();
        $response = $this->get(route('branches.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'address',
                ],
            ],
        ]);
    }

    public function test_get_one_branch_ok(): void
    {
        $branch = \App\Models\Branch::factory()->create();
        $response = $this->get(route('branches.show', $branch->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'address',
            ],
        ]);
    }

    public function test_get_one_branch_not_found(): void
    {
        $response = $this->get(route('branches.show', 10));
        $response->assertStatus(404);
    }

    public function test_create_branch_ok(): void
    {
        $data = \App\Models\Branch::factory()->make()->toArray();
        $response = $this->post(route('branches.store'), $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'address',
            ],
        ]);
    }

    public function test_create_branch_validation_error(): void
    {
        $data = [];
        $response = $this->post(route('branches.store'), $data);
        $response->assertStatus(422);
    }

    public function test_create_branch_validation_error_name_exists(): void
    {
        $branch = \App\Models\Branch::factory()->create();
        $data = \App\Models\Branch::factory()->make(['name' => $branch->name])->toArray();
        $response = $this->post(route('branches.store'), $data);
        $response->assertStatus(422);
    }

    public function test_create_branch_validation_error_name_required(): void
    {
        $data = [
            'name' => '',
            'address' => '',
        ];
        $response = $this->post(route('branches.store'), $data);
        $response->assertStatus(422);
    }

    public function test_delete_branch_ok(): void
    {
        $branch = \App\Models\Branch::factory()->create();
        $response = $this->delete(route('branches.destroy', $branch->id));
        $response->assertStatus(204);
    }

    public function test_delete_branch_not_found(): void
    {
        $response = $this->delete(route('branches.destroy', 10));
        $response->assertStatus(404);
    }


    public function test_update_branch_ok(): void
    {
        $branch = \App\Models\Branch::factory()->create();
        $data = \App\Models\Branch::factory()->make()->toArray();
        $response = $this->put(route('branches.update', $branch->id), $data);
        $response->assertStatus(200);
    }


    public function test_update_branch_validation_error(): void
    {
        $branch = \App\Models\Branch::factory()->create();
        $data = [];
        $response = $this->put(route('branches.update', $branch->id), $data);
        $response->assertStatus(422);
    }

    public function test_update_branch_validation_error_name_exists(): void
    {
        $branch = \App\Models\Branch::factory()->create();
        $branch2 = \App\Models\Branch::factory()->create();
        $data = \App\Models\Branch::factory()->make(['name' => $branch2->name])->toArray();
        $response = $this->put(route('branches.update', $branch->id), $data);
        $response->assertStatus(422);
    }

    public function test_update_branch_validation_error_name_required(): void
    {
        $branch = \App\Models\Branch::factory()->create();
        $data = [
            'name' => '',
            'address' => '',
        ];
        $response = $this->put(route('branches.update', $branch->id), $data);
        $response->assertStatus(422);
    }

    public function test_update_branch_not_found(): void
    {
        $data = \App\Models\Branch::factory()->make()->toArray();
        $response = $this->put(route('branches.update', 10), $data);
        $response->assertStatus(404);
    }
}
