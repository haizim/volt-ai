<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;

final class ProductService
{
    public static function save(Request $request): Product
    {
        return Product::create($request->all());
    }

    public static function get(int $id): Product
    {
        return Product::findOrFail($id);
    }

    public static function update(Request $request, int $id): Product
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());

        return $product;
    }

    public static function delete(int $id): bool
    {
        $product = Product::findOrFail($id);

        return $product->delete();
    }
}
