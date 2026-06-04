<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name'       => $this->faker->name(),
            'username'   => $this->faker->unique()->userName(),
            'email'      => $this->faker->safeEmail(),
            'password'   => Hash::make('password'),
            'role'       => Role::Employee,
        ];
    }

    public function admin(): static
    {
        return $this->state(['role' => Role::Admin]);
    }

    public function manager(): static
    {
        return $this->state(['role' => Role::Manager]);
    }
}
