<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use DatabaseTransactions;

    private Company $company;
    private User $admin;
    private User $manager;
    private User $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company  = Company::factory()->create();
        $this->admin    = User::factory()->admin()->create(['company_id' => $this->company->id]);
        $this->manager  = User::factory()->manager()->create(['company_id' => $this->company->id]);
        $this->employee = User::factory()->create(['company_id' => $this->company->id]);
    }

    public function test_employee_can_list_expenses(): void
    {
        Expense::factory(5)->create([
            'company_id' => $this->company->id,
            'user_id'    => $this->employee->id,
        ]);

        $this->actingAs($this->employee)
            ->getJson('/api/expenses')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['data' => ['data']]);
    }

    public function test_employee_can_create_expense(): void
    {
        $this->actingAs($this->employee)
            ->postJson('/api/expenses', [
                'title'    => 'Flight to Abuja',
                'amount'   => 45000.00,
                'category' => 'Travel',
            ])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Flight to Abuja');

        $this->assertDatabaseHas('expenses', [
            'title'      => 'Flight to Abuja',
            'company_id' => $this->company->id,
            'user_id'    => $this->employee->id,
        ]);
    }

    public function test_manager_can_update_expense(): void
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->company->id,
            'user_id'    => $this->employee->id,
        ]);

        $this->actingAs($this->manager)
            ->putJson("/api/expenses/{$expense->id}", ['title' => 'Updated Title'])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated Title');
    }

    public function test_employee_cannot_update_expense(): void
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->company->id,
            'user_id'    => $this->employee->id,
        ]);

        $this->actingAs($this->employee)
            ->putJson("/api/expenses/{$expense->id}", ['title' => 'Hijacked'])
            ->assertForbidden();
    }

    public function test_admin_can_delete_expense(): void
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->company->id,
            'user_id'    => $this->employee->id,
        ]);

        $this->actingAs($this->admin)
            ->deleteJson("/api/expenses/{$expense->id}")
            ->assertOk();

        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    public function test_manager_cannot_delete_expense(): void
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->company->id,
            'user_id'    => $this->employee->id,
        ]);

        $this->actingAs($this->manager)
            ->deleteJson("/api/expenses/{$expense->id}")
            ->assertForbidden();
    }

    public function test_user_cannot_access_another_company_expenses(): void
    {
        $otherCompany = Company::factory()->create();
        $otherExpense = Expense::factory()->create([
            'company_id' => $otherCompany->id,
            'user_id'    => User::factory()->create(['company_id' => $otherCompany->id])->id,
        ]);

        // Try to update an expense from a different company
        $this->actingAs($this->manager)
            ->putJson("/api/expenses/{$otherExpense->id}", ['title' => 'Cross-tenant attack'])
            ->assertNotFound();
    }

    public function test_update_logs_audit_trail(): void
    {
        $expense = Expense::factory()->create([
            'company_id' => $this->company->id,
            'user_id'    => $this->employee->id,
        ]);

        $this->actingAs($this->admin)
            ->putJson("/api/expenses/{$expense->id}", ['title' => 'Audited Update'])
            ->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'user_id'        => $this->admin->id,
            'company_id'     => $this->company->id,
            'action'         => 'expense.updated',
            'auditable_type' => Expense::class,
            'auditable_id'   => $expense->id,
        ]);
    }
}
