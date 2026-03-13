<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class OutletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'address' => 'required',
            'city' => 'required',
            'province' => 'required',
            'postal_code' => 'required',
        ];
    }
}
