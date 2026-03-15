<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Bank;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

final class BankExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    public function query()
    {
        return Bank::query()->orderBy('name', 'asc');
    }

    public function map($bank): array
    {
        return [
            $bank->code,
            $bank->name,
        ];
    }

    public function headings(): array
    {
        return ['Kode', 'Nama Bank'];
    }

    public function title(): string
    {
        return 'Bank';
    }
}
