<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

test('it has correct fillable attributes', function (): void {
    $product = new Product;

    expect($product->getFillable())->toBe(['user_id', 'name', 'type', 'price', 'weight']);
});

test('it uses soft deletes', function (): void {
    expect(class_uses_recursive(Product::class))->toHaveKey(SoftDeletes::class);
});

test('it belongs to a user', function (): void {
    $product = new Product;

    expect($product->user())->toBeInstanceOf(BelongsTo::class);
});

test('to array', function (): void {
    $product = Product::factory()->for(User::factory()->create())->create()->refresh();

    expect(array_keys($product->toArray()))
        ->toBe(['id', 'user_id', 'name', 'type', 'price', 'weight', 'created_at', 'updated_at', 'deleted_at']);
});
