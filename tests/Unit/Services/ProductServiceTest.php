<?php

declare(strict_types=1);

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

test('it can save a product', function (): void {
    $request = Request::create('/product', 'POST', [
        'name' => 'Laptop Gaming',
        'type' => 'Electronics',
        'price' => 15000000,
        'weight' => 2500,
    ]);

    $product = ProductService::save($request);

    expect($product)->toBeInstanceOf(Product::class)
        ->and($product->name)->toBe('Laptop Gaming')
        ->and($product->type)->toBe('Electronics')
        ->and($product->price)->toBe(15000000)
        ->and($product->weight)->toBe(2500);

    $this->assertDatabaseHas('products', [
        'name' => 'Laptop Gaming',
        'type' => 'Electronics',
        'price' => 15000000,
        'weight' => 2500,
    ]);
});

test('it can get a product by id', function (): void {
    $product = Product::factory()->create();

    $result = ProductService::get($product->id);

    expect($result)->toBeInstanceOf(Product::class)
        ->and($result->id)->toBe($product->id);
});

test('it can update a product', function (): void {
    $product = Product::factory()->create();

    $request = Request::create("/product/{$product->id}", 'PUT', [
        'name' => 'Laptop Updated',
        'type' => 'Electronics',
        'price' => 20000000,
        'weight' => 2000,
    ]);

    $updated = ProductService::update($request, $product->id);

    expect($updated)->toBeInstanceOf(Product::class)
        ->and($updated->name)->toBe('Laptop Updated')
        ->and($updated->price)->toBe(20000000)
        ->and($updated->weight)->toBe(2000);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Laptop Updated',
    ]);
});

test('it can delete a product', function (): void {
    $product = Product::factory()->create();

    ProductService::delete($product->id);

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

test('it can export products', function (): void {
    Product::factory()->count(3)->create();

    $response = ProductService::export();

    expect($response)->toBeInstanceOf(BinaryFileResponse::class)
        ->and($response->getFile()->getExtension())->toBe('xlsx');
});
