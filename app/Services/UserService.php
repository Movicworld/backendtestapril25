<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $users,
    ) {}

    public function list(User $admin): Collection
    {
        return $this->users->allForCompany($admin->company_id);
    }

    public function create(User $admin, array $data): User
    {
        return $this->users->create([
            ...$data,
            'company_id' => $admin->company_id,
        ]);
    }

    public function update(User $admin, int $userId, array $data): User
    {
        $user = $this->users->find($userId, $admin->company_id);

        abort_if(! $user, 404, 'User not found.');

        return $this->users->update($user, $data);
    }
}
