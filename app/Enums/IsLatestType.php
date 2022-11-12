<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OTHER()
 * @method static static LATEST()
 */
final class IsLatestType extends Enum
{
    const OTHER = 0;
    const LATEST = 1;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::OTHER => "最新以外",
            self::LATEST => "最新",
        };
    }
}
