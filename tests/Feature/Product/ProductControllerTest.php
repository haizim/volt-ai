<?php

declare(strict_types=1);

use App\Enums\Permission;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Gate;
use Laravolt\Platform\Models\Permission as LaravoltPermission;

beforeEach(function (): void {
    $this->user = User::factory()->create();

    $this->viewPermission = LaravoltPermission::firstOrCreate(['name' => Permission::PRODUCT_VIEW]);
    $this->managePermission = LaravoltPermission::firstOrCreate(['name' => Permission::PRODUCT_MANAGE]);

    Gate::define(Permission::PRODUCT_VIEW, fn (User $user) => $user->hasPermission($this->viewPermission));
    Gate::define(Permission::PRODUCT_MANAGE, fn (User $user) => $user->hasPermission($this->managePermission));

    $this->withoutMiddleware(ValidateCsrfToken::class);
});

test('it can access product index with view permission', function (): void {
    $this->viewPermission->assignTo($this->user);

    $this->actingAs($this->user)
        ->get(route('product.index'))
        ->assertSuccessful();
});

test('it cannot access product index without view permission', function (): void {
    $this->actingAs($this->user)
        ->get(route('product.index'))
        ->assertStatus(401);
});

test('it can access product create with manage permission', function (): void {
    $this->managePermission->assignTo($this->user);

    $this->actingAs($this->user)
        ->get(route('product.create'))
        ->assertSuccessful();
});

test('it cannot access product create without manage permission', function (): void {
    $this->actingAs($this->user)
        ->get(route('product.create'))
        ->assertStatus(401);
});

test('it can store a product', function (): void {
    $this->managePermission->assignTo($this->user);

    $payload = [
        'name' => 'Test Product',
        'type' => 'physical',
        'price' => 50000,
        'weight' => 200,
    ];

    $this->actingAs($this->user)
        ->post(route('product.store'), $payload)
        ->assertRedirect(route('product.index'));

    $this->assertDatabaseHas('products', array_merge($payload, ['user_id' => $this->user->id]));
});

test('it can display product detail', function (): void {
    $this->viewPermission->assignTo($this->user);

    $product = Product::factory()->for($this->user)->create();

    $this->actingAs($this->user)
        ->get(route('product.show', $product))
        ->assertSuccessful()
        ->assertSee($product->name);
});

test('it can display product edit page', function (): void {
    $this->managePermission->assignTo($this->user);

    $product = Product::factory()->for($this->user)->create();

    $this->actingAs($this->user)
        ->get(route('product.edit', $product))
        ->assertSuccessful()
        ->assertSee($product->name);
});

test('it can update a product', function (): void {
    $this->managePermission->assignTo($this->user);

    $product = Product::factory()->for($this->user)->create();

    $payload = [
        'name' => 'Updated Product',
        'type' => 'digital',
        'price' => 99000,
        'weight' => 0,
    ];

    $this->actingAs($this->user)
        ->put(route('product.update', $product), $payload)
        ->assertRedirect(route('product.index'));

    $this->assertDatabaseHas('products', array_merge($payload, ['id' => $product->id]));
});

test('it can delete a product', function (): void {
    $this->managePermission->assignTo($this->user);

    $product = Product::factory()->for($this->user)->create();

    $this->actingAs($this->user)
        ->delete(route('product.destroy', $product))
        ->assertRedirect(route('product.index'));

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});
