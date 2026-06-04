<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Expense;
use App\Models\User;

class AuditLogService
{
    public function logExpenseUpdate(User $actor, Expense $expense, array $oldValues, array $newValues): void
    {
        AuditLog::create([
            'user_id'        => $actor->id,
            'company_id'     => $actor->company_id,
            'action'         => 'expense.updated',
            'auditable_type' => Expense::class,
            'auditable_id'   => $expense->id,
            'changes'        => [
                'before' => $oldValues,
                'after'  => $newValues,
            ],
        ]);
    }

    public function logExpenseDelete(User $actor, Expense $expense): void
    {
        AuditLog::create([
            'user_id'        => $actor->id,
            'company_id'     => $actor->company_id,
            'action'         => 'expense.deleted',
            'auditable_type' => Expense::class,
            'auditable_id'   => $expense->id,
            'changes'        => [
                'before' => $expense->only(['id', 'title', 'amount', 'category']),
                'after'  => null,
            ],
        ]);
    }
}
