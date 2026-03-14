<?php

declare(strict_types=1);

use App\Models\Bank;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('bank_has_fillable_attributes', function () {
    $bank = Bank::factory()->create([
        'name' => 'Test Bank',
        'code' => 'TEST',
    ]);

    expect($bank->name)->toBe('Test Bank');
    expect($bank->code)->toBe('TEST');
});

test('bank_uses_soft_deletes', function () {
    $bank = Bank::factory()->create();

    $bank->delete();

    $this->assertSoftDeleted('banks', ['id' => $bank->id]);
});

test('bank_factory_creates_valid_bank', function () {
    $bank = Bank::factory()->create();

    expect($bank->name)->not->toBeEmpty();
    expect($bank->code)->not->toBeEmpty();
    expect(strlen($bank->code))->toBeGreaterThanOrEqual(3);
});

test('bank_code_is_unique', function () {
    $bank1 = Bank::factory()->create(['code' => 'UNIQUE']);

    expect(fn () => Bank::factory()->create(['code' => 'UNIQUE']))
        ->toThrow(QueryException::class);
});
