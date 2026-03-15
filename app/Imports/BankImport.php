<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Bank;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

final class BankImport implements SkipsEmptyRows, ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            Bank::updateOrCreate(
                ['code' => $row['kode_bank']],
                [
                    'name' => $row['nama_bank'],
                    'code' => $row['kode_bank'],
                ]
            );
        }
    }

    public function rules(): array
    {
        return [
            'kode_bank' => 'required|string|max:10',
            'nama_bank' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'kode_bank.required' => 'Kode Bank wajib diisi',
            'kode_bank.max' => 'Kode Bank maksimal 10 karakter',
            'nama_bank.required' => 'Nama Bank wajib diisi',
            'nama_bank.max' => 'Nama Bank maksimal 255 karakter',
        ];
    }
}
