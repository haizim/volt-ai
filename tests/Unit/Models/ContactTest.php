<?php

declare(strict_types=1);

use App\Models\Contact;
use App\Models\User;

test('it belongs to a user', function (): void {
    $user = User::factory()->create();
    $contact = Contact::factory()->create(['user_id' => $user->id]);

    expect($contact->user)->toBeInstanceOf(User::class);
    expect($contact->user->id)->toBe($user->id);
});

test('fillable attributes', function (): void {
    $contact = new Contact;

    expect($contact->getFillable())
        ->toBe([
            'first_name',
            'last_name',
            'email',
            'phone',
            'user_id',
        ]);
});

test('it uses soft deletes', function (): void {
    $contact = Contact::factory()->create();
    $contact->delete();

    $this->assertSoftDeleted($contact);
});
