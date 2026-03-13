<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Http\Request;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('it can save a product', function (): void {
    $request = new Request([
        'name' => 'Test Product',
        'type' => 'electronics',
        'price' => 100000,
        'weight' => 1.5,
        'length' => 10.0,
        'width' => 5.0,
        'height' => 3.0,
    ]);

    ProductService::save($request);

    $this->assertDatabaseHas('products', [
        'name' => 'Test Product',
        'type' => 'electronics',
        'price' => 100000,
        'weight' => 1.5,
        'length' => 10.0,
        'width' => 5.0,
        'height' => 3.0,
        'user_id' => $this->user->id,
    ]);
});

test('it can get a product by id', function (): void {
    $product = Product::factory()->create(['user_id' => $this->user->id]);

    $foundProduct = ProductService::get($product->id);

    expect($foundProduct->id)->toBe($product->id);
});

test('it cannot get a product belonging to another user', function (): void {
    $anotherUser = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $anotherUser->id]);

    $foundProduct = ProductService::get($product->id);

    expect($foundProduct)->toBeNull();
});

test('it can update a product', function (): void {
    $product = Product::factory()->create(['user_id' => $this->user->id]);
    $request = new Request([
        'name' => 'Updated Product',
        'type' => 'clothing',
        'price' => 200000,
        'weight' => 2.0,
        'length' => 15.0,
        'width' => 10.0,
        'height' => 5.0,
    ]);

    ProductService::update($request, (string) $product->id);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Product',
        'type' => 'clothing',
        'price' => 200000,
        'weight' => 2.0,
        'length' => 15.0,
        'width' => 10.0,
        'height' => 5.0,
    ]);
});

test('it can delete a product', function (): void {
    $product = Product::factory()->create(['user_id' => $this->user->id]);

    ProductService::delete($product->id);

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});