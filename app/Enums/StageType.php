<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static STAGE_1()
 * @method static static STAGE_2()
 */
final class StageType extends Enum
{
    const STAGE_1 = 1; // 第1段階
    const STAGE_2 = 2; // 第2段階

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::STAGE_1 => "第1段階",
            self::STAGE_2 => "第2段階",
        };
    }
}
