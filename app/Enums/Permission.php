<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @extends Enum<string>
 */
final class Permission extends Enum
{
    // Add your permissions here

    // BANK
    const BANK_VIEW = 'bank:view';

    const BANK_MANAGE = 'bank:manage';

    // BANK ACCOUNT
    const BANK_ACCOUNT_VIEW = 'bank-account:view';

    const BANK_ACCOUNT_MANAGE = 'bank-account:manage';

    // PRODUCT
    const PRODUCT_VIEW = 'product:view';

    const PRODUCT_MANAGE = 'product:manage';
}
