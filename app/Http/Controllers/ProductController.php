<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ProductController
{
    public function index(): View
    {
        return view('products.index');
    }

    public function create(): View
    {
        Gate::authorize(Permission::PRODUCT_MANAGE);

        return view('products.create');
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        ProductService::save($request);

        return redirect()->route('products.index')
            ->withSuccess('Product berhasil ditambahkan');
    }

    public function show(string $id): View
    {
        $product = ProductService::get((int) $id);

        return view('products.show', compact('product'));
    }

    public function edit(string $id): View
    {
        Gate::authorize(Permission::PRODUCT_MANAGE);
        $product = ProductService::get((int) $id);

        return view('products.edit', compact('product'));
    }

    public function update(ProductRequest $request, string $id): RedirectResponse
    {
        ProductService::update($request, (int) $id);

        return redirect()->route('products.index')
            ->withSuccess('Product berhasil diperbarui');
    }

    public function destroy(Product $product): RedirectResponse
    {
        ProductService::delete((int) $product->id);

        return redirect()->route('products.index')
            ->withSuccess('Product berhasil dihapus');
    }

    public function export(): BinaryFileResponse
    {
        Gate::authorize(Permission::PRODUCT_VIEW);

        return ProductService::export();
    }
}
