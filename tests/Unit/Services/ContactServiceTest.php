<?php

declare(strict_types=1);

use App\Models\Contact;
use App\Models\User;
use App\Services\ContactService;
use Illuminate\Http\Request;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('it can save a contact', function (): void {
    $request = new Request([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '123456789',
    ]);

    ContactService::save($request);

    $this->assertDatabaseHas('contacts', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '123456789',
        'user_id' => $this->user->id,
    ]);
});

test('it can get a contact by id', function (): void {
    $contact = Contact::factory()->create(['user_id' => $this->user->id]);

    $foundContact = ContactService::get($contact->id);

    expect($foundContact->id)->toBe($contact->id);
});

test('it cannot get a contact belonging to another user', function (): void {
    $anotherUser = User::factory()->create();
    $contact = Contact::factory()->create(['user_id' => $anotherUser->id]);

    $foundContact = ContactService::get($contact->id);

    expect($foundContact)->toBeNull();
});

test('it can update a contact', function (): void {
    $contact = Contact::factory()->create(['user_id' => $this->user->id]);
    $request = new Request([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane@example.com',
        'phone' => '987654321',
    ]);

    ContactService::update($request, (string) $contact->id);

    $this->assertDatabaseHas('contacts', [
        'id' => $contact->id,
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email' => 'jane@example.com',
        'phone' => '987654321',
    ]);
});

test('it can delete a contact', function (): void {
    $contact = Contact::factory()->create(['user_id' => $this->user->id]);

    ContactService::delete($contact->id);

    $this->assertSoftDeleted('contacts', ['id' => $contact->id]);
});
