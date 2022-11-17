<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static MAN()
 * @method static static WOMAN()
 */
final class Gender extends Enum
{
    const MAN = 1;
    const WOMAN = 2;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::MAN => "男",
            self::WOMAN => "女",
        };
    }
}
