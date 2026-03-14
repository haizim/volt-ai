<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bank>
 */
final class BankFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bankNames = [
            'Bank Central Asia (BCA)',
            'Bank Mandiri',
            'Bank Rakyat Indonesia (BRI)',
            'Bank Negara Indonesia (BNI)',
            'Bank CIMB Niaga',
            'Bank Danamon',
            'Bank Permata',
            'Bank Panin',
            'Bank UOB',
            'Bank Maybank',
        ];

        $bankCodes = [
            'BCA',
            'MDR',
            'BRI',
            'BNI',
            'CIMB',
            'DANAMON',
            'PERMATA',
            'PANIN',
            'UOB',
            'MAYBANK',
        ];

        $index = fake()->unique()->numberBetween(0, count($bankNames) - 1);

        return [
            'name' => $bankNames[$index],
            'code' => $bankCodes[$index],
        ];
    }
}
