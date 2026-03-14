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
        $codeUnique = 'unique:banks,code';

        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            $codeUnique .= ','.$this->bank;
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:10', $codeUnique],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama Bank',
            'code' => 'Kode Bank',
        ];
    }
}
