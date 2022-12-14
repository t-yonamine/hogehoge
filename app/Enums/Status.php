<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static DISABLED()
 * @method static static ENABLED()
 */
final class Status extends Enum
{
    const DISABLED = 0;
    const ENABLED = 1;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::DISABLED => "無効",
            self::ENABLED => "有効",
        };
    }
}
