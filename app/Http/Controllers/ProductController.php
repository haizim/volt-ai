<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\ProductService;

final class ProductController
{
    public function index()
    {
        return view('product.index');
    }

    public function create()
    {
        return view('product.create');
    }

    public function store(ProductRequest $request)
    {
        ProductService::save($request);

        return redirect()->route('product.index')->withSuccess('Produk berhasil disimpan');
    }

    public function show(string $id)
    {
        $product = ProductService::get($id);

        return view('product.show', compact('product'));
    }

    public function edit(string $id)
    {
        $product = ProductService::get($id);

        return view('product.edit', compact('product'));
    }

    public function update(ProductRequest $request, string $id)
    {
        ProductService::update($request, $id);

        return redirect()->route('product.index')->withSuccess('Produk berhasil diperbarui');
    }

    public function destroy(Product $product)
    {
        ProductService::delete($product->id);

        return redirect()->route('product.index')->withSuccess('Produk berhasil dihapus');
    }
}
