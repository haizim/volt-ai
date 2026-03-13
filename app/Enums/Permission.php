<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<string>
 */
final class Permission extends Enum
{
    // CONTACT
    const CONTACT_VIEW = 'contact:view';

    const CONTACT_MANAGE = 'contact:manage';

    // PRODUCT
    const PRODUCT_VIEW = 'product:view';

    const PRODUCT_MANAGE = 'product:manage';

    // OUTLET
    const OUTLET_VIEW = 'outlet:view';

    const OUTLET_MANAGE = 'outlet:manage';

    // BANK
    const BANK_VIEW = 'bank:view';

    const BANK_MANAGE = 'bank:manage';
}
