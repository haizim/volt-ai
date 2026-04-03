<?php

use App\Enums\Permission;

return [
    'Menu' => [
        'order' => 1,
        'menu' => [
            'Products' => [
                'route' => 'product.index',
                'active' => 'product/*',
                'icon' => 'gift',
                'permissions' => [Permission::PRODUCT_VIEW],
            ],
        ],
    ],
];
