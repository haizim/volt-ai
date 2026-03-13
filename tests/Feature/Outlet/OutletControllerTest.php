<?php

declare(strict_types=1);

namespace Tests\Feature\Outlet;

use App\Enums\Permission;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Laravolt\Platform\Models\Permission as LaravoltPermission;
use Laravolt\Platform\Models\Role;

beforeEach(function (): void {
    $this->user = User::factory()->create();

    $this->viewPermission = LaravoltPermission::firstOrCreate(['name' => Permission::OUTLET_VIEW]);
    $this->managePermission = LaravoltPermission::firstOrCreate(['name' => Permission::OUTLET_MANAGE]);

    $this->viewerRole = Role::firstOrCreate(['name' => 'OutletViewer']);
    $this->viewerRole->addPermission($this->viewPermission);

    $this->managerRole = Role::firstOrCreate(['name' => 'OutletManager']);
    $this->managerRole->addPermission($this->viewPermission);
    $this->managerRole->addPermission($this->managePermission);

    Gate::define(Permission::OUTLET_VIEW, function ($user) {
        return $user->hasPermission(Permission::OUTLET_VIEW);
    });
    Gate::define(Permission::OUTLET_MANAGE, function ($user) {
        return $user->hasPermission(Permission::OUTLET_MANAGE);
    });

    $this->withoutMiddleware(ValidateCsrfToken::class);
});

function clearPermissionCache(User $user)
{
    Cache::forget("users.{$user->getKey()}.permissions");
}

test('it can access outlet index with view permission', function (): void {
    $this->user->assignRole($this->viewerRole);
    clearPermissionCache($this->user);

    $this->actingAs($this->user->fresh())
        ->get(route('outlet.index'))
        ->assertSee('Outlet')
        ->assertSee('Nama')
        ->assertSee('Kota')
        ->assertSee('Provinsi')
        ->assertStatus(200);
});

test('it cannot access outlet index without view permission', function (): void {
    $this->actingAs($this->user->fresh())
        ->get(route('outlet.index'))
        ->assertStatus(401);
});

test('it can access outlet create with manage permission', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);

    $this->actingAs($this->user->fresh())
        ->get(route('outlet.create'))
        ->assertOk()
        ->assertViewIs('outlet.create');
});

test('it can store an outlet', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);

    $payload = [
        'name' => 'Test Outlet',
        'address' => 'Jl. Test Address No. 123',
        'city' => 'Jakarta',
        'province' => 'DKI Jakarta',
        'postal_code' => '12345',
    ];

    $this->actingAs($this->user->fresh())
        ->post(route('outlet.store'), $payload)
        ->assertRedirect(route('outlet.index'))
        ->assertSessionHas('success', 'Outlet berhasil disimpan');

    $this->assertDatabaseHas('outlets', $payload + ['user_id' => $this->user->id]);
});

test('it can display outlet detail', function (): void {
    $this->user->assignRole($this->viewerRole);
    clearPermissionCache($this->user);
    $outlet = Outlet::factory()->create(['user_id' => $this->user->id]);

    $this->actingAs($this->user->fresh())
        ->get(route('outlet.show', $outlet->id))
        ->assertOk()
        ->assertViewIs('outlet.show')
        ->assertSee($outlet->name);
});

test('it can display outlet edit page', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);
    $outlet = Outlet::factory()->create(['user_id' => $this->user->id]);

    $this->actingAs($this->user->fresh())
        ->get(route('outlet.edit', $outlet->id))
        ->assertOk()
        ->assertViewIs('outlet.edit')
        ->assertSee($outlet->name);
});

test('it can update an outlet', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);
    $outlet = Outlet::factory()->create(['user_id' => $this->user->id]);

    $payload = [
        'name' => 'Updated Outlet',
        'address' => 'Jl. Updated Address No. 456',
        'city' => 'Bandung',
        'province' => 'Jawa Barat',
        'postal_code' => '40123',
    ];

    $this->actingAs($this->user->fresh())
        ->put(route('outlet.update', $outlet->id), $payload)
        ->assertRedirect(route('outlet.index'))
        ->assertSessionHas('success', 'Outlet berhasil diperbarui');

    $this->assertDatabaseHas('outlets', $payload + ['id' => $outlet->id]);
});

test('it can delete an outlet', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);
    $outlet = Outlet::factory()->create(['user_id' => $this->user->id]);

    $this->actingAs($this->user->fresh())
        ->delete(route('outlet.destroy', $outlet->id))
        ->assertRedirect(route('outlet.index'))
        ->assertSessionHas('success', 'Outlet berhasil dihapus');

    $this->assertSoftDeleted('outlets', ['id' => $outlet->id]);
});