<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OD()
 * @method static static K2()
 */
final class TestType extends Enum
{
    const OD = "od";
    const K2 = "k2";


    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::OD => "OD式",
            self::K2 => "K2式",
        };
    }
}
