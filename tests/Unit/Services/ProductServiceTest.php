<?php

declare(strict_types=1);

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->productData = [
        'name' => 'Test Product Service',
        'type' => 'electronics',
        'price' => 100000,
        'weight' => 500,
    ];
});

test('it_can_save_a_product', function () {
    $request = Request::create('/products', 'POST', $this->productData);

    $product = ProductService::save($request);

    expect($product->name)->toBe('Test Product Service');
    expect($product->type)->toBe('electronics');
    expect($product->price)->toBe(100000);
    expect($product->weight)->toBe(500);
    $this->assertDatabaseHas('products', $this->productData);
});

test('it_can_get_a_product_by_id', function () {
    $product = Product::factory()->create($this->productData);

    $retrievedProduct = ProductService::get($product->id);

    expect($retrievedProduct->id)->toBe($product->id);
    expect($retrievedProduct->name)->toBe($product->name);
    expect($retrievedProduct->type)->toBe($product->type);
});

test('it_throws_exception_when_getting_non_existent_product', function () {
    ProductService::get(999);
})->throws(ModelNotFoundException::class);

test('it_can_update_a_product', function () {
    $product = Product::factory()->create($this->productData);

    $updatedData = [
        'name' => 'Updated Product Service',
        'type' => 'clothing',
        'price' => 200000,
        'weight' => 300,
    ];

    $request = Request::create("/products/{$product->id}", 'PUT', $updatedData);

    $updatedProduct = ProductService::update($request, $product->id);

    expect($updatedProduct->name)->toBe('Updated Product Service');
    expect($updatedProduct->type)->toBe('clothing');
    expect($updatedProduct->price)->toBe(200000);
    expect($updatedProduct->weight)->toBe(300);
    $this->assertDatabaseHas('products', array_merge(['id' => $product->id], $updatedData));
});

test('it_throws_exception_when_updating_non_existent_product', function () {
    $request = Request::create('/products/999', 'PUT', $this->productData);

    ProductService::update($request, 999);
})->throws(ModelNotFoundException::class);

test('it_can_delete_a_product', function () {
    $product = Product::factory()->create($this->productData);

    $result = ProductService::delete($product->id);

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

test('it_throws_exception_when_deleting_non_existent_product', function () {
    ProductService::delete(999);
})->throws(ModelNotFoundException::class);
