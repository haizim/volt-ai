<?php

declare(strict_types=1);

use App\Enums\Permission;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Laravolt\Platform\Models\Permission as LaravoltPermission;
use Laravolt\Platform\Models\Role;

beforeEach(function (): void {
    $this->viewPermission = LaravoltPermission::firstOrCreate(['name' => Permission::PRODUCT_VIEW]);
    $this->managePermission = LaravoltPermission::firstOrCreate(['name' => Permission::PRODUCT_MANAGE]);

    $this->adminRole = Role::firstOrCreate(['name' => 'ProductAdmin']);
    $this->adminRole->addPermission($this->viewPermission);
    $this->adminRole->addPermission($this->managePermission);

    $this->viewerRole = Role::firstOrCreate(['name' => 'ProductViewer']);
    $this->viewerRole->addPermission($this->viewPermission);

    Gate::define(Permission::PRODUCT_VIEW, fn ($user) => $user->hasPermission(Permission::PRODUCT_VIEW));
    Gate::define(Permission::PRODUCT_MANAGE, fn ($user) => $user->hasPermission(Permission::PRODUCT_MANAGE));

    $this->adminUser = User::factory()->create();
    $this->adminUser->assignRole($this->adminRole);

    $this->viewerUser = User::factory()->create();
    $this->viewerUser->assignRole($this->viewerRole);

    $this->adminToken = $this->adminUser->createToken('test-token')->plainTextToken;
    $this->viewerToken = $this->viewerUser->createToken('test-token')->plainTextToken;
});

// --- Index & Search ---

test('it can get list of products', function (): void {
    Product::factory()->count(3)->create();

    $this->withToken($this->adminToken)
        ->getJson('/api/product')
        ->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => ['id', 'name', 'type', 'price', 'weight'],
            ],
        ])
        ->assertJsonPath('success', true)
        ->assertJsonCount(3, 'data');
});

test('it can get empty list of products', function (): void {
    $this->withToken($this->adminToken)
        ->getJson('/api/product')
        ->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonCount(0, 'data');
});

test('it can search products by name', function (): void {
    Product::factory()->create(['name' => 'Laptop Gaming', 'type' => 'Electronics']);
    Product::factory()->create(['name' => 'Mouse Wireless', 'type' => 'Electronics']);

    $this->withToken($this->adminToken)
        ->getJson('/api/product?name=Laptop Gaming')
        ->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Laptop Gaming');
});

test('it can search products by type', function (): void {
    Product::factory()->create(['name' => 'Laptop Gaming', 'type' => 'Electronics']);
    Product::factory()->create(['name' => 'Kaos Polos', 'type' => 'Fashion']);

    $this->withToken($this->adminToken)
        ->getJson('/api/product?type=Fashion')
        ->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Kaos Polos');
});

test('it returns empty results when search has no match', function (): void {
    Product::factory()->create(['name' => 'Laptop Gaming']);

    $this->withToken($this->adminToken)
        ->getJson('/api/product?name=Non Existent')
        ->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonCount(0, 'data');
});

// --- Show ---

test('it can show single product', function (): void {
    $product = Product::factory()->create(['name' => 'Laptop Gaming']);

    $this->withToken($this->adminToken)
        ->getJson("/api/product/{$product->id}")
        ->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'name', 'type', 'price', 'weight'],
        ])
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.id', $product->id)
        ->assertJsonPath('data.name', 'Laptop Gaming');
});

test('it returns not found when showing non existent product', function (): void {
    $this->withToken($this->adminToken)
        ->getJson('/api/product/999')
        ->assertNotFound();
});

// --- Store ---

test('it can create a product', function (): void {
    $data = [
        'name' => 'Laptop Gaming',
        'type' => 'Electronics',
        'price' => 15000000,
        'weight' => 2500,
    ];

    $this->withToken($this->adminToken)
        ->postJson('/api/product', $data)
        ->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'name', 'type', 'price', 'weight'],
        ])
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'Laptop Gaming')
        ->assertJsonPath('data.type', 'Electronics');

    $this->assertDatabaseHas('products', $data);
});

test('it fails validation when creating product without required fields', function (): void {
    $this->withToken($this->adminToken)
        ->postJson('/api/product', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'type', 'price', 'weight']);
});

test('it cannot create product without manage permission', function (): void {
    $this->withToken($this->viewerToken)
        ->postJson('/api/product', [
            'name' => 'Laptop Gaming',
            'type' => 'Electronics',
            'price' => 15000000,
            'weight' => 2500,
        ])
        ->assertForbidden();
});

// --- Update ---

test('it can update a product', function (): void {
    $product = Product::factory()->create();
    $updateData = [
        'name' => 'Laptop Updated',
        'type' => 'Electronics',
        'price' => 20000000,
        'weight' => 2000,
    ];

    $this->withToken($this->adminToken)
        ->putJson("/api/product/{$product->id}", $updateData)
        ->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'Laptop Updated')
        ->assertJsonPath('data.price', 20000000);

    $this->assertDatabaseHas('products', array_merge(['id' => $product->id], $updateData));
});

test('it returns not found when updating non existent product', function (): void {
    $this->withToken($this->adminToken)
        ->putJson('/api/product/999', [
            'name' => 'Updated',
            'type' => 'Electronics',
            'price' => 10000,
            'weight' => 500,
        ])
        ->assertNotFound();
});

test('it cannot update product without manage permission', function (): void {
    $product = Product::factory()->create();

    $this->withToken($this->viewerToken)
        ->putJson("/api/product/{$product->id}", [
            'name' => 'Updated',
            'type' => 'Electronics',
            'price' => 10000,
            'weight' => 500,
        ])
        ->assertForbidden();
});

// --- Destroy ---

test('it can delete a product', function (): void {
    $product = Product::factory()->create();

    $this->withToken($this->adminToken)
        ->deleteJson("/api/product/{$product->id}")
        ->assertSuccessful()
        ->assertJsonPath('success', true);

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

test('it returns not found when deleting non existent product', function (): void {
    $this->withToken($this->adminToken)
        ->deleteJson('/api/product/999')
        ->assertNotFound();
});

test('it cannot delete product without manage permission', function (): void {
    $product = Product::factory()->create();

    $this->withToken($this->viewerToken)
        ->deleteJson("/api/product/{$product->id}")
        ->assertForbidden();
});

// --- Authentication ---

test('it cannot access api without authentication', function (): void {
    $this->getJson('/api/product')
        ->assertUnauthorized();
});
