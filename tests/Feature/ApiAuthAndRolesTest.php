<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthAndRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_bearer_token(): void
    {
        $this->seed(\Database\Seeders\UserSeeder::class);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'projects@company.test',
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'token_type', 'user' => ['id', 'name', 'email', 'role']])
            ->assertJsonPath('user.role', 'projects');
    }

    public function test_org_user_cannot_access_projects(): void
    {
        $this->seed(\Database\Seeders\UserSeeder::class);

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => 'org@company.test',
            'password' => 'password',
        ])->json('token');

        $this->withToken($token)
            ->getJson('/api/v1/projects')
            ->assertForbidden();
    }

    public function test_projects_user_can_list_projects(): void
    {
        $this->seed(\Database\Seeders\UserSeeder::class);
        $this->seed(\Database\Seeders\CompanySeeder::class);

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => 'projects@company.test',
            'password' => 'password',
        ])->json('token');

        $this->withToken($token)
            ->getJson('/api/v1/projects')
            ->assertOk();
    }

    public function test_admin_can_access_any_section(): void
    {
        $this->seed(\Database\Seeders\UserSeeder::class);
        $this->seed(\Database\Seeders\CompanySeeder::class);

        $token = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@company.test',
            'password' => 'password',
        ])->json('token');

        $this->withToken($token)->getJson('/api/v1/projects')->assertOk();
        $this->withToken($token)->getJson('/api/v1/org/departments')->assertOk();
        $this->withToken($token)->getJson('/api/v1/assets/hardware-assets')->assertOk();
    }
}
