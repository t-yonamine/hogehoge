<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static DRLONE()
 * @method static static DRLTWO()
 * @method static static DRLTHREE()
 */
final class DrlType extends Enum
{
    const DRLMT = 1;
    const DRLAT = 2;
    const DRLROAD = 3;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::DRLMT => "所内MT",
            self::DRLAT => "所内AT",
            self::DRLROAD => "自主経路",
        };
    }
}
