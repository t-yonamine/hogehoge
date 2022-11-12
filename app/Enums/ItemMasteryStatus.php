<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static WAITING()
 * @method static static COMPLETED()
 */
final class ItemMasteryStatus extends Enum
{
    const WAITING = 0;
    const COMPLETED = 1;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::WAITING => "教習待、教習中",
            self::COMPLETED => "教習済",
        };
    }
}
