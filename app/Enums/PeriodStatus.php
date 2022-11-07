<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static NEW()
 * @method static static IMPLEMENTED()
 * @method static static APPROVED()
 */
final class PeriodStatus extends Enum
{
    const NEW = 1;
    const IMPLEMENTED = 2;
    const APPROVED = 3;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::NEW => "新規",
            self::IMPLEMENTED => "実施済",
            self::APPROVED => "承認済",
        };
    }
}
