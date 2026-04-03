<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class ProductService
{
    public static function save(Request $request): Product
    {
        return DB::transaction(function () use ($request) {
            return Product::create($request->all());
        });
    }

    public static function get(int|string $id): Product
    {
        return Product::findOrFail($id);
    }

    public static function update(Request $request, int|string $id): Product
    {
        return DB::transaction(function () use ($request, $id) {
            $product = Product::findOrFail($id);
            $product->update($request->all());

            return $product->refresh();
        });
    }

    public static function delete(int|string $id): void
    {
        DB::transaction(function () use ($id) {
            $product = Product::findOrFail($id);
            $product->delete();
        });
    }
}
