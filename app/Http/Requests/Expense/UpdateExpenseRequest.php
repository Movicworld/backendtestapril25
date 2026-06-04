<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'    => ['sometimes', 'string', 'max:255'],
            'amount'   => ['sometimes', 'numeric', 'min:0.01'],
            'category' => ['sometimes', 'string', 'max:100'],
        ];
    }
}
