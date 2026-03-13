<?php

declare(strict_types=1);

use App\Models\Outlet;
use App\Models\User;
use App\Services\OutletService;
use Illuminate\Http\Request;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('it can save an outlet', function (): void {
    $request = new Request([
        'name' => 'Test Outlet',
        'address' => 'Jl. Test Address No. 123',
        'city' => 'Jakarta',
        'province' => 'DKI Jakarta',
        'postal_code' => '12345',
    ]);

    OutletService::save($request);

    $this->assertDatabaseHas('outlets', [
        'name' => 'Test Outlet',
        'address' => 'Jl. Test Address No. 123',
        'city' => 'Jakarta',
        'province' => 'DKI Jakarta',
        'postal_code' => '12345',
        'user_id' => $this->user->id,
    ]);
});

test('it can get an outlet by id', function (): void {
    $outlet = Outlet::factory()->create(['user_id' => $this->user->id]);

    $foundOutlet = OutletService::get($outlet->id);

    expect($foundOutlet->id)->toBe($outlet->id);
});

test('it cannot get an outlet belonging to another user', function (): void {
    $anotherUser = User::factory()->create();
    $outlet = Outlet::factory()->create(['user_id' => $anotherUser->id]);

    $foundOutlet = OutletService::get($outlet->id);

    expect($foundOutlet)->toBeNull();
});

test('it can update an outlet', function (): void {
    $outlet = Outlet::factory()->create(['user_id' => $this->user->id]);
    $request = new Request([
        'name' => 'Updated Outlet',
        'address' => 'Jl. Updated Address No. 456',
        'city' => 'Bandung',
        'province' => 'Jawa Barat',
        'postal_code' => '40123',
    ]);

    OutletService::update($request, (string) $outlet->id);

    $this->assertDatabaseHas('outlets', [
        'id' => $outlet->id,
        'name' => 'Updated Outlet',
        'address' => 'Jl. Updated Address No. 456',
        'city' => 'Bandung',
        'province' => 'Jawa Barat',
        'postal_code' => '40123',
    ]);
});

test('it can delete an outlet', function (): void {
    $outlet = Outlet::factory()->create(['user_id' => $this->user->id]);

    OutletService::delete($outlet->id);

    $this->assertSoftDeleted('outlets', ['id' => $outlet->id]);
});