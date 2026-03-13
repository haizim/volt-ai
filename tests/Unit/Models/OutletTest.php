<?php

declare(strict_types=1);

use App\Models\Outlet;
use App\Models\User;

test('it belongs to a user', function (): void {
    $user = User::factory()->create();
    $outlet = Outlet::factory()->create(['user_id' => $user->id]);

    expect($outlet->user)->toBeInstanceOf(User::class);
    expect($outlet->user->id)->toBe($user->id);
});

test('fillable attributes', function (): void {
    $outlet = new Outlet;

    expect($outlet->getFillable())
        ->toBe([
            'name',
            'address',
            'city',
            'province',
            'postal_code',
            'user_id',
        ]);
});

test('it uses soft deletes', function (): void {
    $outlet = Outlet::factory()->create();
    $outlet->delete();

    $this->assertSoftDeleted($outlet);
});