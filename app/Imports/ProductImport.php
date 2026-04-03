<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

final class ProductImport implements SkipsEmptyRows, ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $collection): void
    {
        foreach ($collection as $row) {
            Product::updateOrCreate(
                ['name' => $row['nama_produk']],
                [
                    'name' => $row['nama_produk'],
                    'type' => $row['tipe'],
                    'price' => $row['harga'],
                    'weight' => $row['berat'],
                ]
            );
        }
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'nama_produk' => 'required|string|max:255',
            'tipe' => 'required|string|max:255',
            'harga' => 'required|numeric',
            'berat' => 'required|numeric',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function customValidationMessages(): array
    {
        return [
            'nama_produk.required' => 'Nama Produk wajib diisi.',
            'nama_produk.max' => 'Nama Produk maksimal 255 karakter.',
            'tipe.required' => 'Tipe wajib diisi.',
            'tipe.max' => 'Tipe maksimal 255 karakter.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'berat.required' => 'Berat wajib diisi.',
            'berat.numeric' => 'Berat harus berupa angka.',
        ];
    }
}
