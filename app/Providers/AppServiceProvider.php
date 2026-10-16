<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Livewire\Chart\ShowcaseBarChart;
use App\Http\Livewire\Statistic\ShowcaseUserCount;
use App\Http\Livewire\Table\ShowcaseTable;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     */
    public const string HOME = '/home';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::component('table.showcase-table', ShowcaseTable::class);
        Livewire::component('statistic.showcase-user-count', ShowcaseUserCount::class);
        Livewire::component('chart.showcase-bar-chart', ShowcaseBarChart::class);
    }
}
