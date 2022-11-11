<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static DRLONE()
 * @method static static DRLTWO()
 * @method static static DRLTHREE()
 */
final class AbsentType extends Enum
{
    const NOT_APPLICABLE = null;
    const PRESENT = 0;
    const ABSENT = 1;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::NOT_APPLICABLE => "所内MT",
            self::PRESENT => "所内AT",
            self::ABSENT => "自主経路",
        };
    }
}
