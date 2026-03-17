<?php

declare(strict_types=1);

use App\Enums\Permission;
use App\Models\Bank;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravolt\Platform\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->viewPermission = Laravolt\Platform\Models\Permission::firstOrCreate(['name' => Permission::BANK_VIEW]);
    $this->managePermission = Laravolt\Platform\Models\Permission::firstOrCreate(['name' => Permission::BANK_MANAGE]);

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

    $this->token = $this->adminUser->createToken('test-token')->plainTextToken;
});

test('it_can_get_list_of_banks', function () {
    Bank::factory()->count(3)->create();

    $response = $this->withToken($this->token)
        ->getJson('/api/banks');

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'code',
                ],
            ],
        ])
        ->assertJsonPath('success', true)
        ->assertJsonCount(3, 'data');
});

test('it_can_search_banks_by_name', function () {
    Bank::factory()->create([
        'name' => 'Bank Central Asia',
        'code' => 'BCA',
    ]);

    Bank::factory()->create([
        'name' => 'Bank Mandiri',
        'code' => 'MDR',
    ]);

    $response = $this->withToken($this->token)
        ->getJson('/api/banks?name=Bank Central Asia');

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Bank Central Asia')
        ->assertJsonPath('data.0.code', 'BCA');
});

test('it_can_search_banks_by_code', function () {
    Bank::factory()->create([
        'name' => 'Bank Central Asia',
        'code' => 'BCA',
    ]);

    Bank::factory()->create([
        'name' => 'Bank Mandiri',
        'code' => 'MDR',
    ]);

    $response = $this->withToken($this->token)
        ->getJson('/api/banks?code=MDR');

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Bank Mandiri')
        ->assertJsonPath('data.0.code', 'MDR');
});

test('it_can_search_banks_by_name_and_code', function () {
    Bank::factory()->create([
        'name' => 'Bank Central Asia',
        'code' => 'BCA',
    ]);

    Bank::factory()->create([
        'name' => 'Bank Mandiri',
        'code' => 'MDR',
    ]);

    $response = $this->withToken($this->token)
        ->getJson('/api/banks?name=Bank Central Asia&code=BCA');

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Bank Central Asia')
        ->assertJsonPath('data.0.code', 'BCA');
});

test('it_can_show_single_bank', function () {
    $bank = Bank::factory()->create([
        'name' => 'Bank Test',
        'code' => 'TST',
    ]);

    $response = $this->withToken($this->token)
        ->getJson("/api/banks/{$bank->id}");

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'code',
            ],
        ])
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.id', $bank->id)
        ->assertJsonPath('data.name', 'Bank Test')
        ->assertJsonPath('data.code', 'TST');
});

test('it_returns_not_found_when_showing_non_existent_bank', function () {
    $response = $this->withToken($this->token)
        ->getJson('/api/banks/999');

    $response->assertNotFound();
});

test('it_can_create_a_bank', function () {
    $bankData = [
        'name' => 'New Bank',
        'code' => 'NEW',
    ];

    $response = $this->withToken($this->token)
        ->postJson('/api/banks', $bankData);

    $response->assertSuccessful()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'code',
            ],
        ])
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'New Bank')
        ->assertJsonPath('data.code', 'NEW');

    $this->assertDatabaseHas('banks', $bankData);
});

test('it_fails_validation_when_creating_bank_without_name', function () {
    $response = $this->withToken($this->token)
        ->postJson('/api/banks', [
            'code' => 'NEW',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('it_fails_validation_when_creating_bank_without_code', function () {
    $response = $this->withToken($this->token)
        ->postJson('/api/banks', [
            'name' => 'New Bank',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['code']);
});

test('it_fails_validation_when_creating_bank_with_duplicate_code', function () {
    Bank::factory()->create([
        'code' => 'BCA',
        'name' => 'Bank Central Asia',
    ]);

    $response = $this->withToken($this->token)
        ->postJson('/api/banks', [
            'name' => 'Another Bank',
            'code' => 'BCA',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['code']);
});

test('it_can_update_a_bank', function () {
    $bank = Bank::factory()->create([
        'name' => 'Old Name',
        'code' => 'OLD',
    ]);

    $updateData = [
        'name' => 'Updated Name',
        'code' => 'UPD',
    ];

    $response = $this->withToken($this->token)
        ->putJson("/api/banks/{$bank->id}", $updateData);

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.code', 'UPD');

    $this->assertDatabaseHas('banks', array_merge(['id' => $bank->id], $updateData));
});

test('it_returns_not_found_when_updating_non_existent_bank', function () {
    $response = $this->withToken($this->token)
        ->putJson('/api/banks/999', [
            'name' => 'Updated Name',
            'code' => 'UPD',
        ]);

    $response->assertNotFound();
});

test('it_fails_validation_when_updating_bank_with_duplicate_code', function () {
    Bank::factory()->create([
        'code' => 'BCA',
        'name' => 'Bank Central Asia',
    ]);

    $bank = Bank::factory()->create([
        'code' => 'MDR',
        'name' => 'Bank Mandiri',
    ]);

    $response = $this->withToken($this->token)
        ->putJson("/api/banks/{$bank->id}", [
            'name' => 'Updated Bank',
            'code' => 'BCA',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['code']);
});

test('it_can_update_bank_with_same_code', function () {
    $bank = Bank::factory()->create([
        'code' => 'BCA',
        'name' => 'Bank Central Asia',
    ]);

    $response = $this->withToken($this->token)
        ->putJson("/api/banks/{$bank->id}", [
            'name' => 'Bank Central Asia Updated',
            'code' => 'BCA',
        ]);

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.name', 'Bank Central Asia Updated')
        ->assertJsonPath('data.code', 'BCA');
});

test('it_can_delete_a_bank', function () {
    $bank = Bank::factory()->create([
        'name' => 'Bank To Delete',
        'code' => 'DEL',
    ]);

    $response = $this->withToken($this->token)
        ->deleteJson("/api/banks/{$bank->id}");

    $response->assertSuccessful()
        ->assertJsonPath('success', true);

    $this->assertSoftDeleted('banks', [
        'id' => $bank->id,
    ]);
});

test('it_returns_not_found_when_deleting_non_existent_bank', function () {
    $response = $this->withToken($this->token)
        ->deleteJson('/api/banks/999');

    $response->assertNotFound();
});

test('it_cannot_access_api_without_authentication', function () {
    $response = $this->getJson('/api/banks');

    $response->assertUnauthorized();
});

test('it_can_get_empty_list_of_banks', function () {
    $response = $this->withToken($this->token)
        ->getJson('/api/banks');

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonCount(0, 'data');
});

test('it_returns_empty_results_when_search_with_non_matching_name', function () {
    Bank::factory()->create([
        'name' => 'Bank Central Asia',
        'code' => 'BCA',
    ]);

    $response = $this->withToken($this->token)
        ->getJson('/api/banks?name=Non Existent Bank');

    $response->assertSuccessful()
        ->assertJsonPath('success', true)
        ->assertJsonCount(0, 'data');
});
