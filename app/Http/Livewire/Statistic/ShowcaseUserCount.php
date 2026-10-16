<?php

declare(strict_types=1);

namespace App\Http\Livewire\Statistic;

use App\Models\User;
use Laravolt\Ui\Statistic;

final class ShowcaseUserCount extends Statistic
{
    public string $label = 'Total Users';

    /** @link https://fomantic-ui.com/globals/site.html#colors  */
    public ?string $color = 'blue';

    /** @link https://fontawesome.com/icons?d=gallery&p=2&s=duotone&m=pro */
    public ?string $icon = 'users';

    public function value(): int|string
    {
        return User::count();
    }
}
