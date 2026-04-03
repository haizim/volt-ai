<?php

declare(strict_types=1);

use App\Enums\Permission;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Gate;
use Laravolt\Platform\Models\Permission as LaravoltPermission;
use Laravolt\Platform\Models\Role;

beforeEach(function (): void {
    $this->user = User::factory()->create();

    $this->viewPermission = LaravoltPermission::firstOrCreate(['name' => Permission::PRODUCT_VIEW]);
    $this->managePermission = LaravoltPermission::firstOrCreate(['name' => Permission::PRODUCT_MANAGE]);

    $this->viewerRole = Role::firstOrCreate(['name' => 'ProductViewer']);
    $this->viewerRole->addPermission($this->viewPermission);

    $this->managerRole = Role::firstOrCreate(['name' => 'ProductManager']);
    $this->managerRole->addPermission($this->viewPermission);
    $this->managerRole->addPermission($this->managePermission);

    Gate::define(Permission::PRODUCT_VIEW, function (User $user): bool {
        return $user->hasPermission(Permission::PRODUCT_VIEW);
    });

    Gate::define(Permission::PRODUCT_MANAGE, function (User $user): bool {
        return $user->hasPermission(Permission::PRODUCT_MANAGE);
    });

    $this->withoutMiddleware(ValidateCsrfToken::class);
});

test('it can access product index with view permission', function (): void {
    $this->user->assignRole($this->viewerRole);

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
    $this->user->assignRole($this->managerRole);

    $this->actingAs($this->user)
        ->get(route('product.create'))
        ->assertSuccessful();
});

test('it cannot access product create without manage permission', function (): void {
    $this->user->assignRole($this->viewerRole);

    $this->actingAs($this->user)
        ->get(route('product.create'))
        ->assertStatus(401);
});

test('it can store a product', function (): void {
    $this->user->assignRole($this->managerRole);

    $payload = [
        'name' => 'Laptop Gaming',
        'type' => 'Electronics',
        'price' => 15000000,
        'weight' => 2500,
    ];

    $this->actingAs($this->user)
        ->post(route('product.store'), $payload)
        ->assertRedirect(route('product.index'));

    $this->assertDatabaseHas('products', $payload);
});

test('it can display product detail', function (): void {
    $this->user->assignRole($this->viewerRole);

    $product = Product::factory()->create();

    $this->actingAs($this->user)
        ->get(route('product.show', $product))
        ->assertSuccessful()
        ->assertSee($product->name);
});

test('it can display product edit page', function (): void {
    $this->user->assignRole($this->managerRole);

    $product = Product::factory()->create();

    $this->actingAs($this->user)
        ->get(route('product.edit', $product))
        ->assertSuccessful()
        ->assertSee($product->name);
});

test('it can update a product', function (): void {
    $this->user->assignRole($this->managerRole);

    $product = Product::factory()->create();

    $payload = [
        'name' => 'Laptop Updated',
        'type' => 'Electronics',
        'price' => 20000000,
        'weight' => 2000,
    ];

    $this->actingAs($this->user)
        ->put(route('product.update', $product), $payload)
        ->assertRedirect(route('product.index'));

    $this->assertDatabaseHas('products', array_merge(['id' => $product->id], $payload));
});

test('it can delete a product', function (): void {
    $this->user->assignRole($this->managerRole);

    $product = Product::factory()->create();

    $this->actingAs($this->user)
        ->delete(route('product.destroy', $product))
        ->assertRedirect(route('product.index'));

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

test('it can export products with view permission', function (): void {
    $this->user->assignRole($this->viewerRole);

    Product::factory()->count(3)->create();

    $this->actingAs($this->user)
        ->get(route('product.export'))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('it cannot export products without view permission', function (): void {
    $this->actingAs($this->user)
        ->get(route('product.export'))
        ->assertStatus(401);
});
