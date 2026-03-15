<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Bank;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

final class BankAccountImportTemplateExport implements FromCollection, ShouldAutoSize, WithTitle
{
    /**
     * @return Collection
     */
    public function collection()
    {
        $bank = Bank::first();

        $headers = [
            'Kode Bank',
            'Nama Bank',
            'Nomor Rekening',
            'Atas Nama',
        ];

        if ($bank) {
            $exampleRow = [
                $bank->code,
                '=VLOOKUP(A2, Bank!A:B, 2, FALSE)',
                random_int(100000000, 999999999),
                fake()->name(),
            ];
        } else {
            $exampleRow = [
                'BCA',
                '=VLOOKUP(A2, Bank!A:B, 2, FALSE)',
                random_int(100000000, 999999999),
                fake()->name(),
            ];
        }

        return collect([$headers, $exampleRow]);
    }

    public function title(): string
    {
        return 'Import';
    }
}
