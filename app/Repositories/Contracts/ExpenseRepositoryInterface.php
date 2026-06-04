<?php

namespace App\Repositories\Contracts;

use App\Models\Expense;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ExpenseRepositoryInterface
{
    public function paginate(int $companyId, array $filters): LengthAwarePaginator;

    public function find(int $id, int $companyId): ?Expense;

    public function create(array $data): Expense;

    public function update(Expense $expense, array $data): Expense;

    public function delete(Expense $expense): bool;

    public function allForCompany(int $companyId): \Illuminate\Database\Eloquent\Collection;
}
