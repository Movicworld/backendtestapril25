<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function allForCompany(int $companyId): Collection
    {
        return User::with('company:id,name')
            ->forCompany($companyId)
            ->latest()
            ->get();
    }

    public function find(int $id, int $companyId): ?User
    {
        return User::forCompany($companyId)->find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->refresh();
    }
}
