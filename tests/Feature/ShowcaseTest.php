<?php

use App\Models\User;

it('renders showcase page successfully', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('showcase'))
        ->assertSuccessful();
});
