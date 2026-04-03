<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProductImportRequest;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ProductController
{
    public function index(): View
    {
        return view('product.index');
    }

    public function create(): View
    {
        return view('product.create');
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        ProductService::save($request);

        return redirect()->route('product.index')
            ->withSuccess('Product created successfully.');
    }

    public function show(string $id): View
    {
        $product = ProductService::get($id);

        return view('product.show', compact('product'));
    }

    public function edit(string $id): View
    {
        $product = ProductService::get($id);

        return view('product.edit', compact('product'));
    }

    public function update(ProductRequest $request, string $id): RedirectResponse
    {
        ProductService::update($request, $id);

        return redirect()->route('product.index')
            ->withSuccess('Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        ProductService::delete($product->id);

        return redirect()->route('product.index')
            ->withSuccess('Product deleted successfully.');
    }

    public function export(): BinaryFileResponse
    {
        return ProductService::export();
    }

    public function template(): BinaryFileResponse
    {
        return ProductService::template();
    }

    public function import(ProductImportRequest $request): RedirectResponse
    {
        ProductService::import($request->file('file'));

        return redirect()->route('product.index')
            ->withSuccess('Product berhasil diimport');
    }
}
