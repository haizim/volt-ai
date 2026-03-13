<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Permission;
use Illuminate\Foundation\Http\FormRequest;

final class BankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(Permission::BANK_MANAGE);
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:banks,code',
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $bankId = $this->route('bank');
            $rules['code'] = 'required|string|max:10|unique:banks,code,'.$bankId;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama bank wajib diisi',
            'code.required' => 'Kode bank wajib diisi',
            'code.unique' => 'Kode bank sudah digunakan',
        ];
    }
}
