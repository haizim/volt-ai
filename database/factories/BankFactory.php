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
        $banks = [
            'BCA' => 'Bank Central Asia',
            'BNI' => 'Bank Negara Indonesia',
            'BRI' => 'Bank Rakyat Indonesia',
            'MANDIRI' => 'Bank Mandiri',
            'CIMB' => 'CIMB Niaga',
            'PERMATA' => 'Bank Permata',
            'UOB' => 'UOB Indonesia',
            'OCBC' => 'OCBC NISP',
            'DANAMON' => 'Bank Danamon',
            'BTPN' => 'BTPN Syariah',
        ];

        $code = fake()->randomElement(array_keys($banks));

        return [
            'name' => $banks[$code],
            'code' => $code,
        ];
    }
}
