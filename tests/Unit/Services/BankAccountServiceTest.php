<?php

declare(strict_types=1);

use App\Models\Bank;
use App\Models\BankAccount;
use App\Services\BankAccountService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->bankAccountData = [
        'bank_id' => Bank::factory()->create()->id,
        'account_number' => '123456789',
        'by_name' => 'Test Account Holder',
    ];
});

test('it_can_save_a_bank_account', function () {
    $request = Request::create('/bank-accounts', 'POST', $this->bankAccountData);

    $bankAccount = BankAccountService::save($request);

    expect($bankAccount->account_number)->toBe('123456789');
    expect($bankAccount->by_name)->toBe('Test Account Holder');
    $this->assertDatabaseHas('bank_accounts', $this->bankAccountData);
});

test('it_can_get_a_bank_account_by_id', function () {
    $bankAccount = BankAccount::factory()->create();

    $retrievedBankAccount = BankAccountService::get($bankAccount->id);

    expect($retrievedBankAccount->id)->toBe($bankAccount->id);
    expect((string) $retrievedBankAccount->account_number)->toBe((string) $bankAccount->account_number);
});

test('it_throws_exception_when_getting_non_existent_bank_account', function () {
    BankAccountService::get(999);
})->throws(ModelNotFoundException::class);

test('it_can_update_a_bank_account', function () {
    $bankAccount = BankAccount::factory()->create();
    $bank = Bank::factory()->create();

    $updatedData = [
        'bank_id' => $bank->id,
        'account_number' => '987654321',
        'by_name' => 'Updated Account Holder',
    ];

    $request = Request::create("/bank-accounts/{$bankAccount->id}", 'PUT', $updatedData);

    $updatedBankAccount = BankAccountService::update($request, $bankAccount->id);

    expect($updatedBankAccount->bank_id)->toBe($bank->id);
    expect($updatedBankAccount->account_number)->toBe('987654321');
    expect($updatedBankAccount->by_name)->toBe('Updated Account Holder');
    $this->assertDatabaseHas('bank_accounts', array_merge(['id' => $bankAccount->id], $updatedData));
});

test('it_throws_exception_when_updating_non_existent_bank_account', function () {
    $request = Request::create('/bank-accounts/999', 'PUT', $this->bankAccountData);

    BankAccountService::update($request, 999);
})->throws(ModelNotFoundException::class);

test('it_can_delete_a_bank_account', function () {
    $bankAccount = BankAccount::factory()->create();

    $result = BankAccountService::delete($bankAccount->id);

    expect($result)->toBeTrue();
    $this->assertSoftDeleted('bank_accounts', ['id' => $bankAccount->id]);
});

test('it_throws_exception_when_deleting_non_existent_bank_account', function () {
    BankAccountService::delete(999);
})->throws(ModelNotFoundException::class);
