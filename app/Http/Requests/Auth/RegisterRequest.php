<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name'  => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'email', 'unique:companies,email'],
            'name'          => ['required', 'string', 'max:255'],
            'username'      => ['required', 'string', 'max:50', 'unique:users,username', 'alpha_dash'],
            'email'         => ['required', 'email'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
