<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use DatabaseTransactions;

    private Company $company;
    private User $admin;
    private User $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company  = Company::factory()->create();
        $this->admin    = User::factory()->admin()->create(['company_id' => $this->company->id]);
        $this->employee = User::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_admin_can_list_users(): void
    {
        $this->actingAs($this->admin)
            ->getJson('/api/users')
            ->assertOk()
            ->assertJsonPath('status', 'success');
    }

    public function test_employee_cannot_list_users(): void
    {
        $this->actingAs($this->employee)
            ->getJson('/api/users')
            ->assertForbidden();
    }

    public function test_admin_can_create_user(): void
    {
        $this->actingAs($this->admin)
            ->postJson('/api/users', [
                'name'     => 'New Employee',
                'username' => 'new_employee_' . uniqid(),
                'email'    => 'newemployee@test.com',
                'password' => 'password123',
                'role'     => 'Employee',
            ])
            ->assertCreated();

        $this->assertDatabaseHas('users', [
            'email'      => 'newemployee@test.com',
            'company_id' => $this->company->id,
        ]);
    }

    public function test_admin_can_update_user_role(): void
    {
        $this->actingAs($this->admin)
            ->putJson("/api/users/{$this->employee->id}", ['role' => Role::Manager->value])
            ->assertOk()
            ->assertJsonPath('data.role', Role::Manager->value);
    }

    public function test_admin_cannot_update_user_from_other_company(): void
    {
        $otherUser = User::factory()->create([
            'company_id' => Company::factory()->create()->id,
        ]);

        $this->actingAs($this->admin)
            ->putJson("/api/users/{$otherUser->id}", ['role' => Role::Admin->value])
            ->assertNotFound();
    }
}
