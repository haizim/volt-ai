<?php

declare(strict_types=1);

namespace Tests\Feature\Bank;

use App\Enums\Permission;
use App\Models\Bank;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Laravolt\Platform\Models\Permission as LaravoltPermission;
use Laravolt\Platform\Models\Role;

beforeEach(function (): void {
    $this->user = User::factory()->create();

    // Create permissions in DB
    $this->viewPermission = LaravoltPermission::firstOrCreate(['name' => Permission::BANK_VIEW]);
    $this->managePermission = LaravoltPermission::firstOrCreate(['name' => Permission::BANK_MANAGE]);

    // Create roles
    $this->viewerRole = Role::firstOrCreate(['name' => 'BankViewer']);
    $this->viewerRole->addPermission($this->viewPermission);

    $this->managerRole = Role::firstOrCreate(['name' => 'BankManager']);
    $this->managerRole->addPermission($this->viewPermission);
    $this->managerRole->addPermission($this->managePermission);

    // Manually define Gates since permissions were created after app boot
    Gate::define(Permission::BANK_VIEW, function ($user) {
        return $user->hasPermission(Permission::BANK_VIEW);
    });
    Gate::define(Permission::BANK_MANAGE, function ($user) {
        return $user->hasPermission(Permission::BANK_MANAGE);
    });

    $this->withoutMiddleware(ValidateCsrfToken::class);
});

function clearPermissionCache(User $user)
{
    Cache::forget("users.{$user->getKey()}.permissions");
}

test('it can access bank index with view permission', function (): void {
    $this->user->assignRole($this->viewerRole);
    clearPermissionCache($this->user);

    $this->actingAs($this->user->fresh())
        ->get(route('banks.index'))
        ->assertSee('Daftar Bank')
        ->assertSee('Nama Bank')
        ->assertSee('Kode')
        ->assertStatus(200);
});

test('it cannot access bank index without view permission', function (): void {
    // No permission given
    $this->actingAs($this->user->fresh())
        ->get(route('banks.index'))
        ->assertStatus(401);
});

test('it can access bank create with manage permission', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);

    $this->actingAs($this->user->fresh())
        ->get(route('banks.create'))
        ->assertOk()
        ->assertViewIs('banks.create');
});

test('it can store a bank', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);

    $payload = [
        'name' => 'Bank Central Asia',
        'code' => 'BCA',
    ];

    $this->actingAs($this->user->fresh())
        ->post(route('banks.store'), $payload)
        ->assertRedirect(route('banks.index'))
        ->assertSessionHas('success', 'Bank berhasil ditambahkan');

    $this->assertDatabaseHas('banks', $payload);
});

test('it can display bank detail', function (): void {
    $this->user->assignRole($this->viewerRole);
    clearPermissionCache($this->user);
    $bank = Bank::factory()->create();

    $this->actingAs($this->user->fresh())
        ->get(route('banks.show', $bank->id))
        ->assertOk()
        ->assertViewIs('banks.show')
        ->assertSee($bank->name)
        ->assertSee($bank->code);
});

test('it can display bank edit page', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);
    $bank = Bank::factory()->create();

    $this->actingAs($this->user->fresh())
        ->get(route('banks.edit', $bank->id))
        ->assertOk()
        ->assertViewIs('banks.edit')
        ->assertSee($bank->name);
});

test('it can update a bank', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);
    $bank = Bank::factory()->create();

    $payload = [
        'name' => 'Bank Negara Indonesia',
        'code' => 'BNI',
    ];

    $this->actingAs($this->user->fresh())
        ->put(route('banks.update', $bank->id), $payload)
        ->assertRedirect(route('banks.index'))
        ->assertSessionHas('success', 'Bank berhasil diperbarui');

    $this->assertDatabaseHas('banks', $payload + ['id' => $bank->id]);
});

test('it can delete a bank', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);
    $bank = Bank::factory()->create();

    $this->actingAs($this->user->fresh())
        ->delete(route('banks.destroy', $bank->id))
        ->assertRedirect(route('banks.index'))
        ->assertSessionHas('success', 'Bank berhasil dihapus');

    $this->assertSoftDeleted('banks', ['id' => $bank->id]);
});
