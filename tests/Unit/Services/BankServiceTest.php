<?php

declare(strict_types=1);

use App\Models\Bank;
use App\Services\BankService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;

test('it can save a bank', function (): void {
    $request = new Request([
        'name' => 'Bank Central Asia',
        'code' => 'BCA',
    ]);

    $bank = BankService::save($request);

    expect($bank->name)->toBe('Bank Central Asia')
        ->and($bank->code)->toBe('BCA');

    $this->assertDatabaseHas('banks', [
        'name' => 'Bank Central Asia',
        'code' => 'BCA',
    ]);
});

test('it can get a bank by id', function (): void {
    $bank = Bank::factory()->create();

    $foundBank = BankService::get($bank->id);

    expect($foundBank->id)->toBe($bank->id)
        ->and($foundBank->name)->toBe($bank->name)
        ->and($foundBank->code)->toBe($bank->code);
});

test('it can update a bank', function (): void {
    $bank = Bank::factory()->create([
        'name' => 'Bank Central Asia',
        'code' => 'BCA',
    ]);

    $request = new Request([
        'name' => 'Bank Negara Indonesia',
        'code' => 'BNI',
    ]);

    $updatedBank = BankService::update($request, $bank->id);

    expect($updatedBank->name)->toBe('Bank Negara Indonesia')
        ->and($updatedBank->code)->toBe('BNI');

    $this->assertDatabaseHas('banks', [
        'id' => $bank->id,
        'name' => 'Bank Negara Indonesia',
        'code' => 'BNI',
    ]);
});

test('it can delete a bank', function (): void {
    $bank = Bank::factory()->create();

    BankService::delete($bank->id);

    $this->assertSoftDeleted('banks', ['id' => $bank->id]);
});

test('it handles unique constraint violation when saving', function (): void {
    Bank::factory()->create(['code' => 'BCA']);

    $request = new Request([
        'name' => 'Bank Central Asia',
        'code' => 'BCA',
    ]);

    $this->expectException(UniqueConstraintViolationException::class);

    BankService::save($request);
});
