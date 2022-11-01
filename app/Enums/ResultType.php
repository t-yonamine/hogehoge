<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static NG()
 * @method static static OK()
 * @method static static CANCEL()
 */
final class ResultType extends Enum
{
    const NG = 0;
    const OK = 1;
    const CANCEL = 2;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::NG => "不合格",
            self::OK => "合格",
            self::CANCEL => "中止",
        };
    }
}
