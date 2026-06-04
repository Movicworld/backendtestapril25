<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    public function register(array $data): array
    {
        $company = Company::create([
            'name'  => $data['company_name'],
            'email' => $data['company_email'],
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'name'       => $data['name'],
            'username'   => $data['username'],
            'email'      => $data['email'],
            'password'   => $data['password'],
            'role'       => Role::Admin,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return compact('user', 'company', 'token');
    }

    public function login(array $credentials): array
    {
        $user = User::where('username', $credentials['username'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return compact('user', 'token');
    }

    public function logout(User $user): void
    {
        $currentAccessToken = $user->currentAccessToken();

        if ($currentAccessToken instanceof PersonalAccessToken) {
            $currentAccessToken->delete();
        }
    }
}
