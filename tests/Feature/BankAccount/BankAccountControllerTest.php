<?php

declare(strict_types=1);

use App\Enums\Permission;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravolt\Platform\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();

    $this->viewPermission = Laravolt\Platform\Models\Permission::firstOrCreate(['name' => Permission::BANK_ACCOUNT_VIEW]);
    $this->managePermission = Laravolt\Platform\Models\Permission::firstOrCreate(['name' => Permission::BANK_ACCOUNT_MANAGE]);

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

test('it_can_access_bank_accounts_index_with_view_permission', function () {
    $response = $this->actingAs($this->normalUser)
        ->get(route('bank-accounts.index'));

    $response->assertStatus(200);
});

test('it_cannot_access_bank_accounts_index_without_view_permission', function () {
    $userWithoutPermission = User::factory()->create();

    $response = $this->actingAs($userWithoutPermission)
        ->get(route('bank-accounts.index'));

    $response->assertStatus(401);
});

test('it_can_access_bank_accounts_create_with_manage_permission', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('bank-accounts.create'));

    $response->assertStatus(200);
});

test('it_cannot_access_bank_accounts_create_without_manage_permission', function () {
    $response = $this->actingAs($this->normalUser)
        ->get(route('bank-accounts.create'));

    $response->assertStatus(403);
});

test('it_can_store_a_bank_account', function () {
    $bank = Bank::factory()->create();
    $bankAccountData = [
        'bank_id' => $bank->id,
        'account_number' => '123456789',
        'by_name' => 'John Doe',
    ];

    $response = $this->actingAs($this->adminUser)
        ->post(route('bank-accounts.store'), $bankAccountData);

    $response->assertRedirect(route('bank-accounts.index'));

    $this->assertDatabaseHas('bank_accounts', $bankAccountData);
});

test('it_cannot_store_a_bank_account_without_manage_permission', function () {
    $bank = Bank::factory()->create();
    $bankAccountData = [
        'bank_id' => $bank->id,
        'account_number' => '123456789',
        'by_name' => 'John Doe',
    ];

    $response = $this->actingAs($this->normalUser)
        ->post(route('bank-accounts.store'), $bankAccountData);

    $response->assertStatus(403);
});

test('it_can_display_bank_account_detail', function () {
    $bankAccount = BankAccount::factory()->create();

    $response = $this->actingAs($this->normalUser)
        ->get(route('bank-accounts.show', $bankAccount->id));

    $response->assertStatus(200);
    $response->assertSee((string) $bankAccount->account_number);
    $response->assertSee($bankAccount->by_name);
});

test('it_cannot_display_bank_account_detail_without_view_permission', function () {
    $bankAccount = BankAccount::factory()->create();
    $userWithoutPermission = User::factory()->create();

    $response = $this->actingAs($userWithoutPermission)
        ->get(route('bank-accounts.show', $bankAccount->id));

    $response->assertStatus(401);
});

test('it_can_display_bank_account_edit_page', function () {
    $bankAccount = BankAccount::factory()->create();

    $response = $this->actingAs($this->adminUser)
        ->get(route('bank-accounts.edit', $bankAccount->id));

    $response->assertStatus(200);
    $response->assertSee((string) $bankAccount->account_number);
    $response->assertSee($bankAccount->by_name);
});

test('it_cannot_display_bank_account_edit_page_without_manage_permission', function () {
    $bankAccount = BankAccount::factory()->create();

    $response = $this->actingAs($this->normalUser)
        ->get(route('bank-accounts.edit', $bankAccount->id));

    $response->assertStatus(403);
});

test('it_can_update_a_bank_account', function () {
    $bankAccount = BankAccount::factory()->create();
    $bank = Bank::factory()->create();

    $updatedData = [
        'bank_id' => $bank->id,
        'account_number' => '987654321',
        'by_name' => 'Jane Smith',
    ];

    $response = $this->actingAs($this->adminUser)
        ->put(route('bank-accounts.update', $bankAccount->id), $updatedData);

    $response->assertRedirect(route('bank-accounts.index'));

    $this->assertDatabaseHas('bank_accounts', array_merge(['id' => $bankAccount->id], $updatedData));
});

test('it_cannot_update_a_bank_account_without_manage_permission', function () {
    $bankAccount = BankAccount::factory()->create();
    $bank = Bank::factory()->create();

    $updatedData = [
        'bank_id' => $bank->id,
        'account_number' => '987654321',
        'by_name' => 'Jane Smith',
    ];

    $response = $this->actingAs($this->normalUser)
        ->put(route('bank-accounts.update', $bankAccount->id), $updatedData);

    $response->assertStatus(403);
});

test('it_can_delete_a_bank_account', function () {
    $bankAccount = BankAccount::factory()->create();

    $response = $this->actingAs($this->adminUser)
        ->delete(route('bank-accounts.destroy', $bankAccount->id));

    $response->assertRedirect(route('bank-accounts.index'));

    $this->assertSoftDeleted('bank_accounts', ['id' => $bankAccount->id]);
});

test('it_cannot_delete_a_bank_account_without_manage_permission', function () {
    $bankAccount = BankAccount::factory()->create();

    $response = $this->actingAs($this->normalUser)
        ->delete(route('bank-accounts.destroy', $bankAccount->id));

    $response->assertStatus(403);
});

test('it_can_export_bank_accounts_with_view_permission', function () {
    $bank = Bank::factory()->create();
    BankAccount::factory()->count(3)->create(['bank_id' => $bank->id]);

    $response = $this->actingAs($this->normalUser)
        ->get(route('bank-accounts.export', ['bank_id' => $bank->id]));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('it_cannot_export_bank_accounts_without_view_permission', function () {
    $bank = Bank::factory()->create();
    BankAccount::factory()->count(3)->create(['bank_id' => $bank->id]);
    $userWithoutPermission = User::factory()->create();

    $response = $this->actingAs($userWithoutPermission)
        ->get(route('bank-accounts.export', ['bank_id' => $bank->id]));

    $response->assertStatus(401);
});
