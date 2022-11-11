<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static ATTENDED()
 * @method static static ABSENT()
 */
final class StudentStatus extends Enum
{
    const ATTENDED = 0;
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
            self::ATTENDED => "出席",
            self::ABSENT => "不在 のどれか",
        };
    }
}
