<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function allForCompany(int $companyId): Collection;

    public function find(int $id, int $companyId): ?User;

    public function create(array $data): User;

    public function update(User $user, array $data): User;
}
