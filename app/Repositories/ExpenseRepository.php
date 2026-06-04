<?php

namespace App\Repositories;

use App\Models\Expense;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    public function paginate(int $companyId, array $filters): LengthAwarePaginator
    {
        return Expense::with('user:id,name,email')
            ->forCompany($companyId)
            ->when($filters['search'] ?? null, fn($q, $search) =>
                $q->where(fn($q) =>
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%")
                )
            )
            ->when($filters['category'] ?? null, fn($q, $cat) => $q->where('category', $cat))
            ->latest()
            ->paginate(15);
    }

    public function find(int $id, int $companyId): ?Expense
    {
        return Expense::with('user:id,name,email')
            ->forCompany($companyId)
            ->find($id);
    }

    public function create(array $data): Expense
    {
        $expense = Expense::create($data);
        $this->clearCache($data['company_id']);

        return $expense->load('user:id,name,email');
    }

    public function update(Expense $expense, array $data): Expense
    {
        $expense->update($data);
        $this->clearCache($expense->company_id);

        return $expense->refresh()->load('user:id,name,email');
    }

    public function delete(Expense $expense): bool
    {
        $companyId = $expense->company_id;
        $deleted   = $expense->delete();
        $this->clearCache($companyId);

        return $deleted;
    }

    public function allForCompany(int $companyId): Collection
    {
        return Cache::remember("expenses.company.{$companyId}", 3600, fn() =>
            Expense::with('user:id,name,email')
                ->forCompany($companyId)
                ->latest()
                ->get()
        );
    }

    private function clearCache(int $companyId): void
    {
        Cache::forget("expenses.company.{$companyId}");
    }
}
