<?php

declare(strict_types=1);

use App\Enums\Permission;
use App\Models\Bank;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravolt\Platform\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();

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

    $this->withoutMiddleware(ValidateCsrfToken::class);
});

test('it_can_access_bank_index_with_view_permission', function () {
    $response = $this->actingAs($this->normalUser)
        ->get(route('banks.index'));

    $response->assertStatus(200);
});

test('it_cannot_access_bank_index_without_view_permission', function () {
    $userWithoutPermission = User::factory()->create();

    $response = $this->actingAs($userWithoutPermission)
        ->get(route('banks.index'));

    $response->assertStatus(401);
});

test('it_can_access_bank_create_with_manage_permission', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('banks.create'));

    $response->assertStatus(200);
});

test('it_cannot_access_bank_create_without_manage_permission', function () {
    $response = $this->actingAs($this->normalUser)
        ->get(route('banks.create'));

    $response->assertStatus(403);
});

test('it_can_store_a_bank', function () {
    $bankData = [
        'name' => 'Bank Test',
        'code' => 'TEST',
    ];

    $response = $this->actingAs($this->adminUser)
        ->post(route('banks.store'), $bankData);

    $response->assertRedirect(route('banks.index'));

    $this->assertDatabaseHas('banks', $bankData);
});

test('it_cannot_store_a_bank_without_manage_permission', function () {
    $bankData = [
        'name' => 'Bank Test',
        'code' => 'TEST',
    ];

    $response = $this->actingAs($this->normalUser)
        ->post(route('banks.store'), $bankData);

    $response->assertStatus(403);
});

test('it_can_display_bank_detail', function () {
    $bank = Bank::factory()->create();

    $response = $this->actingAs($this->normalUser)
        ->get(route('banks.show', $bank->id));

    $response->assertStatus(200);
    $response->assertSee($bank->name);
    $response->assertSee($bank->code);
});

test('it_cannot_display_bank_detail_without_view_permission', function () {
    $bank = Bank::factory()->create();
    $userWithoutPermission = User::factory()->create();

    $response = $this->actingAs($userWithoutPermission)
        ->get(route('banks.show', $bank->id));

    $response->assertStatus(401);
});

test('it_can_display_bank_edit_page', function () {
    $bank = Bank::factory()->create();

    $response = $this->actingAs($this->adminUser)
        ->get(route('banks.edit', $bank->id));

    $response->assertStatus(200);
    $response->assertSee($bank->name);
    $response->assertSee($bank->code);
});

test('it_cannot_display_bank_edit_page_without_manage_permission', function () {
    $bank = Bank::factory()->create();

    $response = $this->actingAs($this->normalUser)
        ->get(route('banks.edit', $bank->id));

    $response->assertStatus(403);
});

test('it_can_update_a_bank', function () {
    $bank = Bank::factory()->create();

    $updatedData = [
        'name' => 'Bank Updated',
        'code' => 'UPDT',
    ];

    $response = $this->actingAs($this->adminUser)
        ->put(route('banks.update', $bank->id), $updatedData);

    $response->assertRedirect(route('banks.index'));

    $this->assertDatabaseHas('banks', array_merge(['id' => $bank->id], $updatedData));
});

test('it_cannot_update_a_bank_without_manage_permission', function () {
    $bank = Bank::factory()->create();

    $updatedData = [
        'name' => 'Bank Updated',
        'code' => 'UPDT',
    ];

    $response = $this->actingAs($this->normalUser)
        ->put(route('banks.update', $bank->id), $updatedData);

    $response->assertStatus(403);
});

test('it_can_delete_a_bank', function () {
    $bank = Bank::factory()->create();

    $response = $this->actingAs($this->adminUser)
        ->delete(route('banks.destroy', $bank->id));

    $response->assertRedirect(route('banks.index'));

    $this->assertSoftDeleted('banks', ['id' => $bank->id]);
});

test('it_can_export_banks_with_view_permission', function () {
    Bank::factory()->count(3)->create();

    $response = $this->actingAs($this->normalUser)
        ->get(route('banks.export'));

    $response->assertStatus(200);
    $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
});

test('it_cannot_export_banks_without_view_permission', function () {
    $userWithoutPermission = User::factory()->create();

    $response = $this->actingAs($userWithoutPermission)
        ->get(route('banks.export'));

    $response->assertStatus(401);
});
