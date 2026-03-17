<?php

declare(strict_types=1);

use App\Exports\ProductTemplateExport;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->productData = [
        'name' => 'Test Product Service',
        'type' => 'electronics',
        'price' => 100000,
        'weight' => 500,
    ];
});

test('it_can_save_a_product', function () {
    $request = Request::create('/products', 'POST', $this->productData);

    $product = ProductService::save($request);

    expect($product->name)->toBe('Test Product Service');
    expect($product->type)->toBe('electronics');
    expect($product->price)->toBe(100000);
    expect($product->weight)->toBe(500);
    $this->assertDatabaseHas('products', $this->productData);
});

test('it_can_get_a_product_by_id', function () {
    $product = Product::factory()->create($this->productData);

    $retrievedProduct = ProductService::get($product->id);

    expect($retrievedProduct->id)->toBe($product->id);
    expect($retrievedProduct->name)->toBe($product->name);
    expect($retrievedProduct->type)->toBe($product->type);
});

test('it_throws_exception_when_getting_non_existent_product', function () {
    ProductService::get(999);
})->throws(ModelNotFoundException::class);

test('it_can_update_a_product', function () {
    $product = Product::factory()->create($this->productData);

    $updatedData = [
        'name' => 'Updated Product Service',
        'type' => 'clothing',
        'price' => 200000,
        'weight' => 300,
    ];

    $request = Request::create("/products/{$product->id}", 'PUT', $updatedData);

    $updatedProduct = ProductService::update($request, $product->id);

    expect($updatedProduct->name)->toBe('Updated Product Service');
    expect($updatedProduct->type)->toBe('clothing');
    expect($updatedProduct->price)->toBe(200000);
    expect($updatedProduct->weight)->toBe(300);
    $this->assertDatabaseHas('products', array_merge(['id' => $product->id], $updatedData));
});

test('it_throws_exception_when_updating_non_existent_product', function () {
    $request = Request::create('/products/999', 'PUT', $this->productData);

    ProductService::update($request, 999);
})->throws(ModelNotFoundException::class);

test('it_can_delete_a_product', function () {
    $product = Product::factory()->create($this->productData);

    $result = ProductService::delete($product->id);

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

test('it_throws_exception_when_deleting_non_existent_product', function () {
    ProductService::delete(999);
})->throws(ModelNotFoundException::class);

test('it_can_export_products', function () {
    Product::factory()->count(3)->create();

    $response = ProductService::export();

    expect($response)->toBeInstanceOf(BinaryFileResponse::class);
    expect($response->getFile()->getExtension())->toBe('xlsx');
});

test('it_can_download_products_template', function () {
    $response = ProductService::template();

    expect($response)->toBeInstanceOf(BinaryFileResponse::class);
    expect($response->getFile()->getExtension())->toBe('xlsx');
});

test('it_can_import_products_from_excel_file', function () {
    Storage::fake('local');

    $initialCount = Product::count();

    $templateFile = 'test_template_import.xlsx';
    Excel::store(
        new ProductTemplateExport,
        $templateFile,
        'local'
    );

    $fullPath = Storage::path($templateFile);
    ProductService::import($fullPath);

    expect(Product::count())->toBeGreaterThan($initialCount);
});

test('it_can_import_products_from_generated_template', function () {
    Storage::fake('local');

    Product::factory()->create([
        'name' => 'TEST_PRODUCT',
        'type' => 'electronics',
        'price' => 1500000,
        'weight' => 500,
    ]);

    $initialCount = Product::count();

    $templateFile = 'test_import_generated.xlsx';
    Excel::store(
        new ProductTemplateExport,
        $templateFile,
        'local'
    );

    $fullPath = Storage::path($templateFile);
    ProductService::import($fullPath);

    expect(Product::count())->toBeGreaterThanOrEqual($initialCount);
});

test('it_can_generate_template_and_import_products', function () {
    Product::factory()->create([
        'name' => 'TEST_PRODUCT',
        'type' => 'electronics',
        'price' => 1500000,
        'weight' => 500,
    ]);

    $templateResponse = ProductService::template();
    $templatePath = $templateResponse->getFile()->getRealPath();

    expect(file_exists($templatePath))->toBeTrue();

    ProductService::import($templatePath);

    expect(Product::where('name', 'TEST_PRODUCT')->exists())->toBeTrue();
});
