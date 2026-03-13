<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;

test('it belongs to a user', function (): void {
    $user = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $user->id]);

    expect($product->user)->toBeInstanceOf(User::class);
    expect($product->user->id)->toBe($user->id);
});

test('fillable attributes', function (): void {
    $product = new Product;

    expect($product->getFillable())
        ->toBe([
            'name',
            'type',
            'price',
            'weight',
            'length',
            'width',
            'height',
            'user_id',
        ]);
});

test('it uses soft deletes', function (): void {
    $product = Product::factory()->create();
    $product->delete();

    $this->assertSoftDeleted($product);
});