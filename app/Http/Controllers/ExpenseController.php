<?php

namespace App\Http\Controllers;

use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Http\Traits\ApiResponse;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    use ApiResponse;

    public function __construct(
        private ExpenseService $service,
        private ExpenseRepositoryInterface $expenses,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $expenses = $this->service->list($request->user(), $request->only(['search', 'category']));

        return $this->success($expenses);
    }

    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $expense = $this->service->create($request->user(), $request->validated());

        return $this->created($expense);
    }

    public function update(UpdateExpenseRequest $request, int $id): JsonResponse
    {
        $expense = $this->expenses->find($id, $request->user()->company_id);

        if (! $expense) {
            return $this->notFound('Expense not found.');
        }

        $updated = $this->service->update($request->user(), $expense, $request->validated());

        return $this->success($updated, 'Expense updated.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $expense = $this->expenses->find($id, $request->user()->company_id);

        if (! $expense) {
            return $this->notFound('Expense not found.');
        }

        $this->service->delete($request->user(), $expense);

        return $this->success(null, 'Expense deleted.');
    }
}
