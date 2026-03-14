<?php

declare(strict_types=1);

use App\Enums\Permission;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravolt\Platform\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();

    $this->viewPermission = Laravolt\Platform\Models\Permission::firstOrCreate(['name' => Permission::PRODUCT_VIEW]);
    $this->managePermission = Laravolt\Platform\Models\Permission::firstOrCreate(['name' => Permission::PRODUCT_MANAGE]);

    $this->adminRole = Role::firstOrCreate(['name' => 'admin']);
    $this->userRole = Role::firstOrCreate(['name' => 'user']);

    $this->adminRole->permissions()->attach($this->viewPermission);
    $this->adminRole->permissions()->attach($this->managePermission);
    $this->userRole->permissions()->attach($this->viewPermission);

    $this->adminUser = User::factory()->create();
    $this->adminUser->roles()->attach($this->adminRole);

    $this->normalUser = User::factory()->create();
    $this->normalUser->roles()->attach($this->userRole);

    Gate::before(function (User $user, string $ability) {
        return $user->roles->flatMap->permissions->contains('name', $ability);
    });

    $this->withoutMiddleware(ValidateCsrfToken::class);
});

test('it_can_access_product_index_with_view_permission', function () {
    $response = $this->actingAs($this->normalUser)
        ->get(route('products.index'));

    $response->assertStatus(200);
});

test('it_cannot_access_product_index_without_view_permission', function () {
    $userWithoutPermission = User::factory()->create();

    $response = $this->actingAs($userWithoutPermission)
        ->get(route('products.index'));

    $response->assertStatus(401);
});

test('it_can_access_product_create_with_manage_permission', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('products.create'));

    $response->assertStatus(200);
});

test('it_cannot_access_product_create_without_manage_permission', function () {
    $response = $this->actingAs($this->normalUser)
        ->get(route('products.create'));

    $response->assertStatus(403);
});

test('it_can_store_a_product', function () {
    $productData = [
        'name' => 'Test Product',
        'type' => 'electronics',
        'price' => 100000,
        'weight' => 500,
    ];

    $response = $this->actingAs($this->adminUser)
        ->post(route('products.store'), $productData);

    $response->assertRedirect(route('products.index'));

    $this->assertDatabaseHas('products', $productData);
});

test('it_cannot_store_a_product_without_manage_permission', function () {
    $productData = [
        'name' => 'Test Product',
        'type' => 'electronics',
        'price' => 100000,
        'weight' => 500,
    ];

    $response = $this->actingAs($this->normalUser)
        ->post(route('products.store'), $productData);

    $response->assertStatus(403);
});

test('it_can_display_product_detail', function () {
    $product = Product::factory()->create();

    $response = $this->actingAs($this->normalUser)
        ->get(route('products.show', $product->id));

    $response->assertStatus(200);
    $response->assertSee($product->name);
    $response->assertSee($product->type);
});

test('it_cannot_display_product_detail_without_view_permission', function () {
    $product = Product::factory()->create();
    $userWithoutPermission = User::factory()->create();

    $response = $this->actingAs($userWithoutPermission)
        ->get(route('products.show', $product->id));

    $response->assertStatus(401);
});

test('it_can_display_product_edit_page', function () {
    $product = Product::factory()->create();

    $response = $this->actingAs($this->adminUser)
        ->get(route('products.edit', $product->id));

    $response->assertStatus(200);
    $response->assertSee($product->name);
    $response->assertSee($product->type);
});

test('it_cannot_display_product_edit_page_without_manage_permission', function () {
    $product = Product::factory()->create();

    $response = $this->actingAs($this->normalUser)
        ->get(route('products.edit', $product->id));

    $response->assertStatus(403);
});

test('it_can_update_a_product', function () {
    $product = Product::factory()->create();

    $updatedData = [
        'name' => 'Updated Product',
        'type' => 'clothing',
        'price' => 200000,
        'weight' => 300,
    ];

    $response = $this->actingAs($this->adminUser)
        ->put(route('products.update', $product->id), $updatedData);

    $response->assertRedirect(route('products.index'));

    $this->assertDatabaseHas('products', array_merge(['id' => $product->id], $updatedData));
});

test('it_cannot_update_a_product_without_manage_permission', function () {
    $product = Product::factory()->create();

    $updatedData = [
        'name' => 'Updated Product',
        'type' => 'clothing',
        'price' => 200000,
        'weight' => 300,
    ];

    $response = $this->actingAs($this->normalUser)
        ->put(route('products.update', $product->id), $updatedData);

    $response->assertStatus(403);
});

test('it_can_delete_a_product', function () {
    $product = Product::factory()->create();

    $response = $this->actingAs($this->adminUser)
        ->delete(route('products.destroy', $product->id));

    $response->assertRedirect(route('products.index'));

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

test('it_can_export_products_with_view_permission', function () {
    Product::factory()->count(3)->create();

    $response = $this->actingAs($this->normalUser)
        ->get(route('products.export'));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('it_cannot_export_products_without_view_permission', function () {
    $userWithoutPermission = User::factory()->create();

    $response = $this->actingAs($userWithoutPermission)
        ->get(route('products.export'));

    $response->assertStatus(401);
});
