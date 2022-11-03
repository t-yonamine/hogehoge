<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static FIRST1()
 * @method static static FIRST2()
 */
final class Seq extends Enum
{
    const FIRST1 = 1;
    const FIRST2 = 2;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::FIRST1 => "1回目",
            self::FIRST2 => "2回目",
        };
    }
}
