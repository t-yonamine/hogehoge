<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static FOR_AUTHENTICATION()
 * @method static static FOR_ORIGINAL()
 */
final class ImageType extends Enum
{
    const FOR_AUTHENTICATION = 0;
    const FOR_ORIGINAL = 1;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::FOR_AUTHENTICATION => "認証用",
            self::FOR_ORIGINAL => "原簿用",
        };
    }
}
