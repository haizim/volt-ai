<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

final class ProductExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    public function query()
    {
        return Product::query()->orderBy('name', 'asc');
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->type,
            $product->price,
            $product->weight,
        ];
    }

    public function headings(): array
    {
        return ['Nama Produk', 'Tipe', 'Harga', 'Berat'];
    }
}
