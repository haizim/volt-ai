<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Permission;
use Illuminate\Foundation\Http\FormRequest;

final class BankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(Permission::BANK_ACCOUNT_MANAGE);
    }

    public function rules(): array
    {
        return [
            'bank_id' => ['required', 'exists:banks,id'],
            'account_number' => ['required', 'string', 'digits:9'],
            'by_name' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'bank_id.required' => 'The bank field is required.',
            'bank_id.exists' => 'The selected bank is invalid.',
            'account_number.required' => 'The account number field is required.',
            'account_number.string' => 'The account number must be a string.',
            'account_number.digits' => 'The account number must be 9 digits.',
            'by_name.required' => 'The account name field is required.',
            'by_name.string' => 'The account name must be a string.',
            'by_name.max' => 'The account name may not be greater than 255 characters.',
        ];
    }
}
