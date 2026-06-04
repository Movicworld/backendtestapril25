<?php

namespace App\Http\Requests\User;

use App\Enums\Role;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username', 'alpha_dash'],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(
                    fn($q) => $q->where('company_id',  $this->companyId())
                )
            ],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', new Enum(Role::class)],
        ];
    }

    protected function companyId()
    {
        return $this->user()?->company_id;
    }
}
