<?php

declare(strict_types=1);

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

final class ProductTemplateExport implements FromCollection, ShouldAutoSize, WithTitle
{
    public function collection(): Collection
    {
        return collect([
            ['Nama Produk', 'Tipe', 'Harga', 'Berat'],
            ['Contoh Produk A', 'Elektronik', 1500000, 500],
        ]);
    }

    public function title(): string
    {
        return 'Import';
    }
}
