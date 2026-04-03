<?php

declare(strict_types=1);

use App\Enums\Permission;
use App\Exports\ProductTemplateExport;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Laravolt\Platform\Models\Permission as LaravoltPermission;
use Laravolt\Platform\Models\Role;
use Maatwebsite\Excel\Facades\Excel;

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

test('it can download product template with view permission', function (): void {
    $this->user->assignRole($this->viewerRole);

    $this->actingAs($this->user)
        ->get(route('product.template'))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('it cannot download product template without view permission', function (): void {
    $this->actingAs($this->user)
        ->get(route('product.template'))
        ->assertStatus(401);
});

test('it can see import section with manage permission', function (): void {
    $this->user->assignRole($this->managerRole);

    $this->actingAs($this->user)
        ->get(route('product.index'))
        ->assertSuccessful()
        ->assertSee('Import');
});

test('it cannot see import section without manage permission', function (): void {
    $this->user->assignRole($this->viewerRole);

    $this->actingAs($this->user)
        ->get(route('product.index'))
        ->assertSuccessful()
        ->assertDontSee('Import');
});

test('it can import products with valid file', function (): void {
    $this->user->assignRole($this->managerRole);

    Storage::fake('local');

    $templateFile = 'test_import_product.xlsx';
    Excel::store(new ProductTemplateExport, $templateFile, 'local');

    $file = new UploadedFile(
        Storage::path($templateFile),
        'product-import.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $this->actingAs($this->user)
        ->post(route('product.import'), ['file' => $file])
        ->assertRedirect(route('product.index'))
        ->assertSessionHasNoErrors();
});

test('it cannot import products without manage permission', function (): void {
    $this->user->assignRole($this->viewerRole);

    Storage::fake('local');

    $templateFile = 'test_import_product_no_perm.xlsx';
    Excel::store(new ProductTemplateExport, $templateFile, 'local');

    $file = new UploadedFile(
        Storage::path($templateFile),
        'product-import.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );

    $this->actingAs($this->user)
        ->post(route('product.import'), ['file' => $file])
        ->assertForbidden();
});

test('it cannot import products with invalid file format', function (): void {
    $this->user->assignRole($this->managerRole);

    $file = UploadedFile::fake()->create('products.pdf', 100, 'application/pdf');

    $this->actingAs($this->user)
        ->post(route('product.import'), ['file' => $file])
        ->assertSessionHasErrors(['file']);
});

test('it cannot import products without file', function (): void {
    $this->user->assignRole($this->managerRole);

    $this->actingAs($this->user)
        ->post(route('product.import'), [])
        ->assertSessionHasErrors(['file']);
});
