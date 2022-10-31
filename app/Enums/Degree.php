<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static TEACHING()
 * @method static static MIKIWAME()
 * @method static static CERTIFICATION()
 */
final class Degree extends Enum
{
    const TEACHING = 1;
    const MIKIWAME = 2;
    const CERTIFICATION = 4;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::TEACHING => "指導資格有",
            self::MIKIWAME => "みきわめ有",
            self::CERTIFICATION => "検定有",
        };
    }
}
