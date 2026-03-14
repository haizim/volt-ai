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
            'Bank Account' => [
                'route' => 'bank-accounts.index',
                'active' => 'bank-accounts/*',
                'icon' => 'credit-card',
                'permissions' => [Permission::BANK_VIEW],
            ],
            'Product' => [
                'route' => 'products.index',
                'active' => 'products/*',
                'icon' => 'gift',
                'permissions' => [Permission::PRODUCT_VIEW],
            ],
        ],
    ],
];
