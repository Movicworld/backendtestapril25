<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'    => ['required', 'string', 'max:255'],
            'amount'   => ['required', 'numeric', 'min:0.01'],
            'category' => ['required', 'string', 'max:100'],
        ];
    }
}
