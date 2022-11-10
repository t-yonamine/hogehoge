<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static UTF8()
 * @method static static SHIFT_JIS()
 */
final class Encoding extends Enum
{
    const UTF8 = "UTF-8";
    const SHIFT_JIS = "SHIFT-JIS";
}
