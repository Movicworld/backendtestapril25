<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\User;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ExpenseService
{
    public function __construct(
        private ExpenseRepositoryInterface $expenses,
        private AuditLogService $auditLog,
    ) {}

    public function list(User $user, array $filters): LengthAwarePaginator
    {
        return $this->expenses->paginate($user->company_id, $filters);
    }

    public function create(User $user, array $data): Expense
    {
        return $this->expenses->create([
            ...$data,
            'user_id'    => $user->id,
            'company_id' => $user->company_id,
        ]);
    }

    public function update(User $actor, Expense $expense, array $data): Expense
    {
        $before = $expense->only(['title', 'amount', 'category']);

        $updated = $this->expenses->update($expense, $data);

        $this->auditLog->logExpenseUpdate($actor, $updated, $before, $updated->only(['title', 'amount', 'category']));

        return $updated;
    }

    public function delete(User $actor, Expense $expense): void
    {
        $this->auditLog->logExpenseDelete($actor, $expense);
        $this->expenses->delete($expense);
    }
}
