<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_register_with_company(): void
    {
        $response = $this->postJson('/api/register', [
            'company_name'          => 'Test Corp',
            'company_email'         => 'corp@test.com',
            'name'                  => 'John Admin',
            'username'              => 'john_testcorp',
            'email'                 => 'john@test.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['data' => ['user', 'company', 'token']]);

        $this->assertDatabaseHas('companies', ['email' => 'corp@test.com']);
        $this->assertDatabaseHas('users', ['username' => 'john_testcorp', 'role' => 'Admin']);
    }

    public function test_user_can_login(): void
    {
        $company = Company::factory()->create();
        $user    = User::factory()->create([
            'company_id' => $company->id,
            'username'   => 'test_user_login',
            'password'   => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'test_user_login',
            'password' => 'secret123',
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['data' => ['token', 'user']]);
    }

    public function test_login_fails_with_wrong_credentials(): void
    {
        $company = Company::factory()->create();
        User::factory()->create(['company_id' => $company->id, 'username' => 'wrong_user_test']);

        $this->postJson('/api/login', [
            'username' => 'wrong_user_test',
            'password' => 'wrongpassword',
        ])->assertStatus(422);
    }

    public function test_user_can_logout(): void
    {
        $company = Company::factory()->create();
        $user    = User::factory()->admin()->create(['company_id' => $company->id]);

        // Create a real token so currentAccessToken() returns a PersonalAccessToken
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk()
            ->assertJsonPath('status', 'success');
    }

    public function test_same_email_can_exist_in_different_companies(): void
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        User::factory()->create([
            'company_id' => $company1->id,
            'username'   => 'victor_co1_' . uniqid(),
            'email'      => 'victor@example.com',
        ]);

        User::factory()->create([
            'company_id' => $company2->id,
            'username'   => 'victor_co2_' . uniqid(),
            'email'      => 'victor@example.com',
        ]);

        // Both users exist with the same email across different companies
        $this->assertDatabaseHas('users', ['email' => 'victor@example.com', 'company_id' => $company1->id]);
        $this->assertDatabaseHas('users', ['email' => 'victor@example.com', 'company_id' => $company2->id]);
    }
}
