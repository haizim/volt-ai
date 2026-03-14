<?php

use App\Enums\Permission;

return [
    'Menu' => [
        'order' => 1,
        'menu' => [
            'Bank' => [
                'route' => 'banks.index',
                'active' => 'banks/*',
                'icon' => 'building',
                'permissions' => [Permission::BANK_VIEW],
            ],
        ],
    ],
];
