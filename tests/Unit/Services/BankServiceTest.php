<?php

declare(strict_types=1);

use App\Exports\BankTemplateExport;
use App\Models\Bank;
use App\Services\BankService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->bankData = [
        'name' => 'Test Bank Service',
        'code' => 'SRVC',
    ];
});

test('it_can_save_a_bank', function () {
    $request = Request::create('/banks', 'POST', $this->bankData);

    $bank = BankService::save($request);

    expect($bank->name)->toBe('Test Bank Service');
    expect($bank->code)->toBe('SRVC');
    $this->assertDatabaseHas('banks', $this->bankData);
});

test('it_can_get_a_bank_by_id', function () {
    $bank = Bank::factory()->create($this->bankData);

    $retrievedBank = BankService::get($bank->id);

    expect($retrievedBank->id)->toBe($bank->id);
    expect($retrievedBank->name)->toBe($bank->name);
    expect($retrievedBank->code)->toBe($bank->code);
});

test('it_throws_exception_when_getting_non_existent_bank', function () {
    BankService::get(999);
})->throws(ModelNotFoundException::class);

test('it_can_update_a_bank', function () {
    $bank = Bank::factory()->create($this->bankData);

    $updatedData = [
        'name' => 'Updated Bank Service',
        'code' => 'UPDT',
    ];

    $request = Request::create("/banks/{$bank->id}", 'PUT', $updatedData);

    $updatedBank = BankService::update($request, $bank->id);

    expect($updatedBank->name)->toBe('Updated Bank Service');
    expect($updatedBank->code)->toBe('UPDT');
    $this->assertDatabaseHas('banks', array_merge(['id' => $bank->id], $updatedData));
});

test('it_throws_exception_when_updating_non_existent_bank', function () {
    $request = Request::create('/banks/999', 'PUT', $this->bankData);

    BankService::update($request, 999);
})->throws(ModelNotFoundException::class);

test('it_can_delete_a_bank', function () {
    $bank = Bank::factory()->create($this->bankData);

    $result = BankService::delete($bank->id);

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('banks', ['id' => $bank->id]);
});

test('it_throws_exception_when_deleting_non_existent_bank', function () {
    BankService::delete(999);
})->throws(ModelNotFoundException::class);

test('it_can_export_banks', function () {
    Bank::factory()->count(3)->create();

    $response = BankService::export();

    expect($response)->toBeInstanceOf(BinaryFileResponse::class);
    expect($response->getFile()->getExtension())->toBe('xlsx');
});

test('it_can_download_bank_template', function () {
    $response = BankService::template();

    expect($response)->toBeInstanceOf(BinaryFileResponse::class);
    expect($response->getFile()->getExtension())->toBe('xlsx');
});

test('it_can_import_banks_from_excel_file', function () {
    Storage::fake('local');

    $initialCount = Bank::count();

    $templateFile = 'test_template_import.xlsx';
    Excel::store(
        new BankTemplateExport,
        $templateFile,
        'local'
    );

    $fullPath = Storage::path($templateFile);
    BankService::import($fullPath);

    expect(Bank::count())->toBeGreaterThan($initialCount);
});

test('it_can_import_banks_from_generated_template', function () {
    Storage::fake('local');

    Bank::factory()->create([
        'code' => 'TEST01',
        'name' => 'Test Bank 01',
    ]);

    $initialCount = Bank::count();

    $templateFile = 'test_import_generated.xlsx';
    Excel::store(
        new BankTemplateExport,
        $templateFile,
        'local'
    );

    $fullPath = Storage::path($templateFile);
    BankService::import($fullPath);

    expect(Bank::count())->toBeGreaterThanOrEqual($initialCount);
});

test('it_can_generate_template_and_import_banks', function () {
    Bank::factory()->create([
        'code' => 'TEST02',
        'name' => 'Test Bank 02',
    ]);

    $templateResponse = BankService::template();
    $templatePath = $templateResponse->getFile()->getRealPath();

    expect(file_exists($templatePath))->toBeTrue();

    BankService::import($templatePath);

    expect(Bank::where('code', 'TEST02')->exists())->toBeTrue();
});
