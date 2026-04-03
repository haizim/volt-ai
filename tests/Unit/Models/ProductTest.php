<?php

declare(strict_types=1);

use App\Models\Product;

test('to array', function (): void {
    $product = Product::factory()->create()->refresh();

    expect(array_keys($product->toArray()))
        ->toBe([
            'id',
            'name',
            'type',
            'price',
            'weight',
            'created_at',
            'updated_at',
        ]);
});

test('it has correct fillable attributes', function (): void {
    $product = new Product;

    expect($product->getFillable())->toBe(['name', 'type', 'price', 'weight']);
});

test('it uses soft deletes', function (): void {
    $product = Product::factory()->create();

    $product->delete();

    $this->assertSoftDeleted('products', ['id' => $product->id]);
    expect(Product::withTrashed()->find($product->id))->not->toBeNull();
});
