<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<string>
 */
final class Permission extends Enum
{
    // Product
    const PRODUCT_VIEW = 'product:view';

    const PRODUCT_MANAGE = 'product:manage';
}
