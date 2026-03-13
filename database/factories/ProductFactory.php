<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'type' => $this->faker->randomElement(['electronics', 'clothing', 'food', 'books']),
            'price' => $this->faker->numberBetween(1000, 1000000),
            'weight' => $this->faker->randomFloat(2, 0.1, 100),
            'length' => $this->faker->randomFloat(2, 1, 200),
            'width' => $this->faker->randomFloat(2, 1, 100),
            'height' => $this->faker->randomFloat(2, 1, 100),
            'user_id' => User::factory(),
        ];
    }
}
