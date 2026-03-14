<?php

declare(strict_types=1);

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('product_has_fillable_attributes', function () {
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'type' => 'electronics',
        'price' => 100000,
        'weight' => 500,
    ]);

    expect($product->name)->toBe('Test Product');
    expect($product->type)->toBe('electronics');
    expect($product->price)->toBe(100000);
    expect($product->weight)->toBe(500);
});

test('product_uses_soft_deletes', function () {
    $product = Product::factory()->create();

    $product->delete();

    $this->assertSoftDeleted('products', ['id' => $product->id]);

    Product::withTrashed()->find($product->id);
});

test('product_can_be_created_by_factory', function () {
    $product = Product::factory()->create();

    expect($product)->toBeInstanceOf(Product::class);
    expect($product->id)->not()->toBeNull();
    $this->assertDatabaseHas('products', ['id' => $product->id]);
});
