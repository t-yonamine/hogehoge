<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static INTERNAL()
 * @method static static EXTERNAL()
 */
final class PublicLevelStatus extends Enum
{
    const INTERNAL = 0;
    const EXTERNAL = 1;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::INTERNAL => "内部",
            self::EXTERNAL => "外部",
        };
    }
}
