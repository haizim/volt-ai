<?php

declare(strict_types=1);

namespace Tests\Feature\Product;

use App\Enums\Permission;
use App\Models\Product;
use App\Models\User;
use BladeUI\Icons\Factory;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Cache;
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

    Gate::define(Permission::PRODUCT_VIEW, function ($user) {
        return $user->hasPermission(Permission::PRODUCT_VIEW);
    });
    Gate::define(Permission::PRODUCT_MANAGE, function ($user) {
        return $user->hasPermission(Permission::PRODUCT_MANAGE);
    });

    $this->withoutMiddleware(ValidateCsrfToken::class);
});

function clearPermissionCache(User $user)
{
    Cache::forget("users.{$user->getKey()}.permissions");
}

test('it can access product index with view permission', function (): void {
    $this->user->assignRole($this->viewerRole);
    clearPermissionCache($this->user);

    $this->actingAs($this->user->fresh())
        ->get(route('product.index'))
        ->assertSee('Produk')
        ->assertSee('Nama')
        ->assertSee('Tipe')
        ->assertSee('Harga')
        ->assertStatus(200);
});

test('it cannot access product index without view permission', function (): void {
    $this->actingAs($this->user->fresh())
        ->get(route('product.index'))
        ->assertStatus(401);
});

test('it can access product create with manage permission', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);

    $this->actingAs($this->user->fresh())
        ->get(route('product.create'))
        ->assertOk()
        ->assertViewIs('product.create');
});

test('it can store a product', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);

    $payload = [
        'name' => 'Test Product',
        'type' => 'electronics',
        'price' => 100000,
        'weight' => 1.5,
        'length' => 10.0,
        'width' => 5.0,
        'height' => 3.0,
    ];

    $this->actingAs($this->user->fresh())
        ->post(route('product.store'), $payload)
        ->assertRedirect(route('product.index'))
        ->assertSessionHas('success', 'Produk berhasil disimpan');

    $this->assertDatabaseHas('products', $payload + ['user_id' => $this->user->id]);
});

test('it can display product detail', function (): void {
    $this->user->assignRole($this->viewerRole);
    clearPermissionCache($this->user);
    $product = Product::factory()->create(['user_id' => $this->user->id]);

    $this->actingAs($this->user->fresh())
        ->get(route('product.show', $product->id))
        ->assertOk()
        ->assertViewIs('product.show')
        ->assertSee($product->name);
});

test('it can display product edit page', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);
    $product = Product::factory()->create(['user_id' => $this->user->id]);

    $this->actingAs($this->user->fresh())
        ->get(route('product.edit', $product->id))
        ->assertOk()
        ->assertViewIs('product.edit')
        ->assertSee($product->name);
});

test('it can update a product', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);
    $product = Product::factory()->create(['user_id' => $this->user->id]);

    $payload = [
        'name' => 'Updated Product',
        'type' => 'clothing',
        'price' => 200000,
        'weight' => 2.0,
        'length' => 15.0,
        'width' => 10.0,
        'height' => 5.0,
    ];

    $this->actingAs($this->user->fresh())
        ->put(route('product.update', $product->id), $payload)
        ->assertRedirect(route('product.index'))
        ->assertSessionHas('success', 'Produk berhasil diperbarui');

    $this->assertDatabaseHas('products', $payload + ['id' => $product->id]);
});

test('it can delete a product', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);
    $product = Product::factory()->create(['user_id' => $this->user->id]);

    $this->actingAs($this->user->fresh())
        ->delete(route('product.destroy', $product->id))
        ->assertRedirect(route('product.index'))
        ->assertSessionHas('success', 'Produk berhasil dihapus');

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});