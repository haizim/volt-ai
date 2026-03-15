<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

final class BankTemplateExport implements FromCollection, ShouldAutoSize, WithTitle
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return collect([
            ['Kode Bank', 'Nama Bank'],
            ['BXY', 'Bank XY'],
        ]);
    }

    public function title(): string
    {
        return 'Import';
    }
}
