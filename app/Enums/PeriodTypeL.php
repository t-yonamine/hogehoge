<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static DRV_LESSON()
 * @method static static LECTURE()
 * @method static static TEST()
 * @method static static WORK()
 */
final class PeriodType extends Enum
{
    const DRV_LESSON = 1;
    const LECTURE = 2;
    const TEST = 3;
    const WORK = 4;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::DRV_LESSON => "技能教習",
            self::LECTURE => "学科教習",
            self::TEST => "検定",
            self::WORK => "業務",
        };
    }
}
