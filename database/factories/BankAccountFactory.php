<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Bank;
use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BankAccount>
 */
final class BankAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bank_id' => Bank::factory(),
            'account_number' => $this->faker->randomNumber(9, true),
            'by_name' => $this->faker->name(),
        ];
    }
}
