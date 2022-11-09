<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static DRV_LESN_1()
 * @method static static DRV_LESN_2()
 * @method static static ASCERTAIN_1()
 * @method static static ASCERTAIN_2()
 * @method static static COMPLTST_ADD_LESN()
 * @method static static GRADTST_ADD_LESN()
 * @method static static OPT_LESN()
 * @method static static FREE_LESN()
 * 
 * @method static static LECTURE_1()
 * @method static static LECTURE_1N()
 * @method static static LECTURE_2()
 * @method static static LECTURE_2N()
 * @method static static LECTURE_1ST()
 * 
 * @method static static EFF_MEAS_1()
 * @method static static EFF_MEAS_1N()
 * @method static static EFF_MEAS_2()
 * @method static static EFF_MEAS_2N()
 * @method static static EFF_MEAS_MIN()
 * @method static static EFF_MEAS_MAX()
 * @method static static COMPLTST()
 * @method static static PLS_TEST()
 * @method static static GRASTST()
 * @method static static DRV_LESSON()
 */
class LaType extends Enum
{
    const DRV_LESN_1 = 1010;
    const DRV_LESN_2 = 1020;
    const ASCERTAIN_1 = 1110;
    const ASCERTAIN_2 = 1120;
    const COMPLTST_ADD_LESN = 1210;
    const GRADTST_ADD_LESN = 1220;
    const OPT_LESN = 1300;
    const FREE_LESN = 1400;

    const LECTURE_1 = 2010;
    const LECTURE_1N = 2011;
    const LECTURE_2 = 2020;
    const LECTURE_2N = 2021;
    const LECTURE_1ST = 2110;

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
            self::DRV_LESN_1 => "技能１",
            self::DRV_LESN_2 => "技能２",
            self::ASCERTAIN_1 => "みきわめ１",
            self::ASCERTAIN_2 => "みきわめ２",
            self::COMPLTST_ADD_LESN => "修検補修",
            self::GRADTST_ADD_LESN => "卒検補修",
            self::OPT_LESN => "任意教習",
            self::FREE_LESN => "自由教習",
            //
            self::LECTURE_1 => "学科１",
            self::LECTURE_1N => "学科１Ｎ",
            self::LECTURE_2 => "学科２",
            self::LECTURE_2N => "学科２Ｎ",
            self::LECTURE_1ST => "先行学科",
            //
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
