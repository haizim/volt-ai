<?php

use App\Enums\Permission;

return [
    'Menu' => [
        'order' => 1,
        'menu' => [
            'Contact' => [
                'route' => 'contact.index',
                'active' => 'contact/*',
                'icon' => 'users',
                'permissions' => [Permission::CONTACT_VIEW],
            ],
            'Product' => [
                'route' => 'product.index',
                'active' => 'product/*',
                'icon' => 'cube',
                'permissions' => [Permission::PRODUCT_VIEW],
            ],
        ],
    ],
];
