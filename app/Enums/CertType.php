<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static CERTIFICATE_COMPLETION()
 * @method static static PROVISIONAL_LICENSE()
 * @method static static GRADUATION_CERTIFICATE()
 */
final class CertType extends Enum
{

    const CERTIFICATE_COMPLETION = 1;
    const PROVISIONAL_LICENSE = 2;
    const GRADUATION_CERTIFICATE = 3;
}
