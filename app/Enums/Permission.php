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
}
