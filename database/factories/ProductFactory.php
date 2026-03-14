<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
final class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'type' => fake()->randomElement(['electronics', 'clothing', 'food', 'books']),
            'price' => fake()->numberBetween(1000, 1000000),
            'weight' => fake()->numberBetween(1, 1000),
        ];
    }
}
