<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Outlet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Outlet>
 */
final class OutletFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'province' => $this->faker->state,
            'postal_code' => $this->faker->postcode,
            'user_id' => User::factory(),
        ];
    }
}
