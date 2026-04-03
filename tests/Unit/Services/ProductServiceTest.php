<?php

declare(strict_types=1);

use App\Models\Product;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Http\Request;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->otherUser = User::factory()->create();
});

test('it can save a product', function (): void {
    $request = Request::create('/products', 'POST', [
        'name' => 'New Product',
        'type' => 'physical',
        'price' => 75000,
        'weight' => 300,
    ]);
    $request->setUserResolver(fn () => $this->user);

    $product = ProductService::save($request);

    expect($product)->toBeInstanceOf(Product::class)
        ->and($product->name)->toBe('New Product')
        ->and($product->type)->toBe('physical')
        ->and($product->price)->toBe(75000)
        ->and($product->weight)->toBe(300)
        ->and($product->user_id)->toBe($this->user->id);
});

test('it can get a product by id', function (): void {
    $product = Product::factory()->for($this->user)->create();

    $found = ProductService::get($product->id);

    expect($found->id)->toBe($product->id);
});

test('it cannot get product belonging to another user', function (): void {
    $product = Product::factory()->for($this->otherUser)->create();

    $request = Request::create('/');
    $request->setUserResolver(fn () => $this->user);

    ProductService::get($product->id, $request);
})->throws(Exception::class);

test('it can update a product', function (): void {
    $product = Product::factory()->for($this->user)->create();

    $request = Request::create('/products/'.$product->id, 'PUT', [
        'name' => 'Updated Product',
        'type' => 'digital',
        'price' => 100000,
        'weight' => 0,
    ]);
    $request->setUserResolver(fn () => $this->user);

    $updated = ProductService::update($request, $product->id);

    expect($updated->name)->toBe('Updated Product')
        ->and($updated->type)->toBe('digital')
        ->and($updated->price)->toBe(100000)
        ->and($updated->weight)->toBe(0);
});

test('it can delete a product', function (): void {
    $product = Product::factory()->for($this->user)->create();

    ProductService::delete($product->id);

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});
