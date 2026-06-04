<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Traits\ApiResponse;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(private UserService $service) {}

    public function index(Request $request): JsonResponse
    {
        $users = $this->service->list($request->user());

        return $this->success($users, 'Users retrieved successfully.');
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->service->create($request->user(), $request->validated());

        return $this->created($user, 'User created successfully.');
    }

    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = $this->service->update($request->user(), $id, $request->validated());

        return $this->success($user, 'User role updated.');
    }
}
