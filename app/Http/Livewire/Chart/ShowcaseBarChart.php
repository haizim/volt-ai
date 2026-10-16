<?php

declare(strict_types=1);

namespace App\Http\Livewire\Chart;

use Laravolt\Charts\Bar;

final class ShowcaseBarChart extends Bar
{
    protected string $title = 'Monthly Activity';

    protected int $height = 350;

    /**
     * Sembunyikan semua element pendukung chart seperti sumbu x-y dan label.
     * Cocok untuk menampilkan chart dalam area yang sempit.
     *
     * @link https://apexcharts.com/docs/options/chart/sparkline/
     */
    protected bool $sparkline = false;

    public function series(): array
    {
        return [
            'Users' => [
                'Jan' => 10,
                'Feb' => 20,
                'Mar' => 15,
                'Apr' => 30,
            ],
            'Orders' => [
                'Jan' => 5,
                'Feb' => 12,
                'Mar' => 25,
                'Apr' => 18,
            ],
        ];
    }
}
