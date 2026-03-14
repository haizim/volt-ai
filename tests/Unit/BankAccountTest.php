<?php

declare(strict_types=1);

use App\Models\Bank;
use App\Models\BankAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('bank_account_has_fillable_attributes', function () {
    $bank = Bank::factory()->create();
    $bankAccount = BankAccount::factory()->create([
        'bank_id' => $bank->id,
        'account_number' => '123456789',
        'by_name' => 'Test Account',
    ]);

    expect($bankAccount->bank_id)->toBeInt();
    expect($bankAccount->account_number)->toBeString();
    expect($bankAccount->by_name)->toBeString();
});

test('bank_account_has_bank_relationship', function () {
    $bank = Bank::factory()->create();
    $bankAccount = BankAccount::factory()->create(['bank_id' => $bank->id]);

    expect($bankAccount->bank)->toBeInstanceOf(Bank::class);
    expect($bankAccount->bank->id)->toBe($bank->id);
});

test('bank_account_uses_soft_deletes', function () {
    $bankAccount = BankAccount::factory()->create();

    $bankAccount->delete();

    $this->assertSoftDeleted('bank_accounts', [
        'id' => $bankAccount->id,
    ]);

    expect(BankAccount::withTrashed()->find($bankAccount->id))->not->toBeNull();
    expect(BankAccount::find($bankAccount->id))->toBeNull();
});

test('bank_account_factory_creates_valid_bank_account', function () {
    $bankAccount = BankAccount::factory()->create();

    expect($bankAccount->account_number)->not->toBeEmpty();
    expect($bankAccount->by_name)->not->toBeEmpty();
    expect($bankAccount->bank_id)->not->toBeEmpty();
});
