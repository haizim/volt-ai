<?php

declare(strict_types=1);

namespace Tests\Feature\Contact;

use App\Enums\Permission;
use App\Models\Contact;
use App\Models\User;
use BladeUI\Icons\Factory;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Laravolt\Platform\Models\Permission as LaravoltPermission;
use Laravolt\Platform\Models\Role;

beforeEach(function (): void {
    $this->user = User::factory()->create();

    // Create permissions in DB
    $this->viewPermission = LaravoltPermission::firstOrCreate(['name' => Permission::CONTACT_VIEW]);
    $this->managePermission = LaravoltPermission::firstOrCreate(['name' => Permission::CONTACT_MANAGE]);

    // Create roles
    $this->viewerRole = Role::firstOrCreate(['name' => 'Viewer']);
    $this->viewerRole->addPermission($this->viewPermission);

    $this->managerRole = Role::firstOrCreate(['name' => 'Manager']);
    $this->managerRole->addPermission($this->viewPermission);
    $this->managerRole->addPermission($this->managePermission);

    // Manually define Gates since permissions were created after app boot
    Gate::define(Permission::CONTACT_VIEW, function ($user) {
        return $user->hasPermission(Permission::CONTACT_VIEW);
    });
    Gate::define(Permission::CONTACT_MANAGE, function ($user) {
        return $user->hasPermission(Permission::CONTACT_MANAGE);
    });

    $this->withoutMiddleware(ValidateCsrfToken::class);
});

function clearPermissionCache(User $user)
{
    Cache::forget("users.{$user->getKey()}.permissions");
}

test('it can access contact index with view permission', function (): void {
    $this->user->assignRole($this->viewerRole);
    clearPermissionCache($this->user);

    $this->actingAs($this->user->fresh())
        ->get(route('contact.index'))
        // ->assertOk()
        ->assertSee('Kontak')
        ->assertSee('Nama Depan')
        ->assertSee('Nama Belakang')
        ->assertSee('Email')
        ->assertSee('No. Telepon')
        ->assertStatus(200);
    // ->assertViewIs('contact.index');
});

test('it cannot access contact index without view permission', function (): void {
    // No permission given
    $this->actingAs($this->user->fresh())
        ->get(route('contact.index'))
        ->assertStatus(401);
});

test('it can access contact create with manage permission', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);

    $this->actingAs($this->user->fresh())
        ->get(route('contact.create'))
        ->assertOk()
        ->assertViewIs('contact.create');
});

test('it can store a contact', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);

    $payload = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '08123456789',
    ];

    $this->actingAs($this->user->fresh())
        ->post(route('contact.store'), $payload)
        ->assertRedirect(route('contact.index'))
        ->assertSessionHas('success', 'Kontak berhasil disimpan');

    $this->assertDatabaseHas('contacts', $payload + ['user_id' => $this->user->id]);
});

test('it can display contact detail', function (): void {
    $this->user->assignRole($this->viewerRole);
    clearPermissionCache($this->user);
    $contact = Contact::factory()->create(['user_id' => $this->user->id]);

    $this->actingAs($this->user->fresh())
        ->get(route('contact.show', $contact->id))
        ->assertOk()
        ->assertViewIs('contact.show')
        ->assertSee($contact->first_name);
});

test('it can display contact edit page', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);
    $contact = Contact::factory()->create(['user_id' => $this->user->id]);

    $this->actingAs($this->user->fresh())
        ->get(route('contact.edit', $contact->id))
        ->assertOk()
        ->assertViewIs('contact.edit')
        ->assertSee($contact->first_name);
});

test('it can update a contact', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);
    $contact = Contact::factory()->create(['user_id' => $this->user->id]);

    $payload = [
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane@example.com',
        'phone' => '08987654321',
    ];

    $this->actingAs($this->user->fresh())
        ->put(route('contact.update', $contact->id), $payload)
        ->assertRedirect(route('contact.index'))
        ->assertSessionHas('success', 'Kontak berhasil diperbarui');

    $this->assertDatabaseHas('contacts', $payload + ['id' => $contact->id]);
});

test('it can delete a contact', function (): void {
    $this->user->assignRole($this->managerRole);
    clearPermissionCache($this->user);
    $contact = Contact::factory()->create(['user_id' => $this->user->id]);

    $this->actingAs($this->user->fresh())
        ->delete(route('contact.destroy', $contact->id))
        ->assertRedirect(route('contact.index'))
        ->assertSessionHas('success', 'Kontak berhasil dihapus');

    $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
});
