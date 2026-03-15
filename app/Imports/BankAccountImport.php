<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Bank;
use App\Models\BankAccount;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithValidation;

final class BankAccountImport implements SkipsEmptyRows, ToCollection, WithHeadingRow, WithMultipleSheets, WithValidation
{
    /**
     * @var array<string>
     */
    protected array $errors = [];

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    /**
     * @param  Collection<int, mixed>  $rows
     */
    public function collection(Collection $rows): void
    {
        $banks = Bank::all();

        foreach ($rows as $row) {
            $bank = $banks->where('code', $row['kode_bank'])->first();

            if (! $bank) {
                $this->errors[] = "Bank dengan kode '{$row['kode_bank']}' tidak ditemukan.";

                continue;
            }

            BankAccount::create([
                'bank_id' => $bank->id,
                'account_number' => $row['nomor_rekening'],
                'by_name' => $row['atas_nama'],
            ]);
        }
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'kode_bank' => 'required|string|exists:banks,code',
            'nama_bank' => 'nullable|string',
            'nomor_rekening' => 'required|numeric',
            'atas_nama' => 'required|string|max:255',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function customValidationMessages(): array
    {
        return [
            'kode_bank.required' => 'Kode Bank wajib diisi.',
            'kode_bank.exists' => 'Kode Bank tidak ditemukan.',
            'nomor_rekening.required' => 'Nomor Rekening wajib diisi.',
            'atas_nama.required' => 'Atas Nama wajib diisi.',
        ];
    }

    /**
     * @return array<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return ! empty($this->errors);
    }
}
