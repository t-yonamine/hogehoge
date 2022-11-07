<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static EFF_MEAS_1()
 * @method static static EFF_MEAS_1N()
 * @method static static EFF_MEAS_2()
 * @method static static EFF_MEAS_2N()
 * @method static static EFF_MEAS_MIN()
 * @method static static EFF_MEAS_MAX()
 */
class LaType extends Enum
{
    // La_type Effect measurement
    const EFF_MEAS_1 = 2210;
    const EFF_MEAS_1N = 2211;
    const EFF_MEAS_2 = 2220;
    const EFF_MEAS_2N = 2221;
    const EFF_MEAS_MIN = 2200;
    const EFF_MEAS_MAX = 2299;
    const COMPLTST = 3110;
    const PL_TEST = 3210;
    const GRADTST = 3320;
    const DRVSKLTST = 3400;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::EFF_MEAS_1, self::EFF_MEAS_1N => "仮免前",
            self::EFF_MEAS_2, self::EFF_MEAS_2N => "卒検前",
            self::EFF_MEAS_MIN => "",
            self::EFF_MEAS_MAX => "",
            self::COMPLTST, self::COMPLTST => "修了検定",
            self::PL_TEST, self::PL_TEST => "仮免許",
            self::GRADTST, self::GRADTST => "卒業検定",
            self::DRVSKLTST, self::DRVSKLTST => "技能審査",
        };
    }
}
