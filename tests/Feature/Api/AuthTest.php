<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('it_can_login_with_valid_credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'token',
            'user' => [
                'id',
                'name',
                'email',
                'username',
            ],
        ])
        ->assertJsonPath('user.email', 'test@example.com');
});

test('it_cannot_login_with_invalid_credentials', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('it_cannot_login_with_missing_email', function () {
    $response = $this->postJson('/api/login', [
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('it_cannot_login_with_missing_password', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

test('it_can_get_authenticated_user', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withToken($token)
        ->getJson('/api/user');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'id',
                'name',
                'email',
                'username',
            ],
        ])
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.email', $user->email);
});

test('it_cannot_get_authenticated_user_without_token', function () {
    $response = $this->getJson('/api/user');

    $response->assertStatus(401);
});

test('it_can_logout', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withToken($token)
        ->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data',
        ])
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Successfully logged out');

    // Verify token is deleted
    $this->assertDatabaseMissing('personal_access_tokens', [
        'tokenable_id' => $user->id,
        'tokenable_type' => get_class($user),
    ]);
});

test('it_cannot_logout_without_token', function () {
    $response = $this->postJson('/api/logout');

    $response->assertStatus(401);
});
