<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductService
{
    public static function save(Request $request)
    {
        $me = auth()->user();

        $product = new Product;
        $product->name = $request->name;
        $product->type = $request->type;
        $product->price = $request->price;
        $product->weight = $request->weight;
        $product->length = $request->length;
        $product->width = $request->width;
        $product->height = $request->height;
        $product->user_id = $me->id;
        $product->save();
    }

    public static function get($id)
    {
        $me = auth()->user();

        return Product::where('user_id', $me->id)->find($id);
    }

    public static function update(Request $request, $id)
    {
        $me = auth()->user();

        $product = Product::where('user_id', $me->id)->find($id);
        $product->name = $request->name;
        $product->type = $request->type;
        $product->price = $request->price;
        $product->weight = $request->weight;
        $product->length = $request->length;
        $product->width = $request->width;
        $product->height = $request->height;
        $product->save();
    }

    public static function delete($id)
    {
        $me = auth()->user();
        Product::where('user_id', $me->id)->find($id)->delete();
    }
}
