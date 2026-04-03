<?php

declare(strict_types=1);

use App\Exports\ProductTemplateExport;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

test('it can save a product', function (): void {
    $request = Request::create('/product', 'POST', [
        'name' => 'Laptop Gaming',
        'type' => 'Electronics',
        'price' => 15000000,
        'weight' => 2500,
    ]);

    $product = ProductService::save($request);

    expect($product)->toBeInstanceOf(Product::class)
        ->and($product->name)->toBe('Laptop Gaming')
        ->and($product->type)->toBe('Electronics')
        ->and($product->price)->toBe(15000000)
        ->and($product->weight)->toBe(2500);

    $this->assertDatabaseHas('products', [
        'name' => 'Laptop Gaming',
        'type' => 'Electronics',
        'price' => 15000000,
        'weight' => 2500,
    ]);
});

test('it can get a product by id', function (): void {
    $product = Product::factory()->create();

    $result = ProductService::get($product->id);

    expect($result)->toBeInstanceOf(Product::class)
        ->and($result->id)->toBe($product->id);
});

test('it can update a product', function (): void {
    $product = Product::factory()->create();

    $request = Request::create("/product/{$product->id}", 'PUT', [
        'name' => 'Laptop Updated',
        'type' => 'Electronics',
        'price' => 20000000,
        'weight' => 2000,
    ]);

    $updated = ProductService::update($request, $product->id);

    expect($updated)->toBeInstanceOf(Product::class)
        ->and($updated->name)->toBe('Laptop Updated')
        ->and($updated->price)->toBe(20000000)
        ->and($updated->weight)->toBe(2000);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Laptop Updated',
    ]);
});

test('it can delete a product', function (): void {
    $product = Product::factory()->create();

    ProductService::delete($product->id);

    $this->assertSoftDeleted('products', ['id' => $product->id]);
});

test('it can export products', function (): void {
    Product::factory()->count(3)->create();

    $response = ProductService::export();

    expect($response)->toBeInstanceOf(BinaryFileResponse::class)
        ->and($response->getFile()->getExtension())->toBe('xlsx');
});

test('it can download product template', function (): void {
    $response = ProductService::template();

    expect($response)->toBeInstanceOf(BinaryFileResponse::class)
        ->and($response->getFile()->getExtension())->toBe('xlsx');
});

test('it can import products from excel file', function (): void {
    Storage::fake('local');

    $initialCount = Product::count();

    $templateFile = 'test_template_import.xlsx';
    Excel::store(new ProductTemplateExport, $templateFile, 'local');

    ProductService::import(Storage::path($templateFile));

    expect(Product::count())->toBeGreaterThan($initialCount);
});

test('it can generate template and import products', function (): void {
    Product::factory()->create([
        'name' => 'Test Produk',
        'type' => 'Electronics',
        'price' => 10000,
        'weight' => 500,
    ]);

    $templateResponse = ProductService::template();
    $templatePath = $templateResponse->getFile()->getRealPath();

    expect(file_exists($templatePath))->toBeTrue();

    ProductService::import($templatePath);

    expect(Product::where('name', 'Test Produk')->exists())->toBeTrue();
});
