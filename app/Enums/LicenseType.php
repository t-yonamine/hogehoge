<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static SL_MVL()
 * @method static static SL_MVL_L()
 * @method static static L_MVL()
 * @method static static L_MVL_L()
 * @method static static M_MVL()
 * @method static static M_MVL_L()
 * @method static static SM_MVL()
 * @method static static SM_MVL_L()
 * @method static static S_MVL_MT()
 * @method static static S_MVL_MT_L()
 * @method static static S_MVL_AT()
 * @method static static SS_MVL()
 * @method static static SS_MVL_L()
 * @method static static TOWING()
 * @method static static TOWING_L()
 * @method static static L_ML()
 * @method static static L_ML_L()
 * @method static static L_ML_AT()
 * @method static static S_ML()
 * @method static static S_ML_L()
 * @method static static S_ML_AT()
 * @method static static MBL()
 * @method static static SL_MVL_2()
 * @method static static L_MVL_2()
 * @method static static M_MVL_2()
 * @method static static S_MVL_2()
 * @method static static TOWING_2()
 * @method static static PL_MVL()
 * @method static static PM_MVL()
 * @method static static PSM_MVL()
 * @method static static PS_MVL_MT()
 */
class LicenseType extends Enum
{
    const SL_MVL = 10100;
    const SL_MVL_L = 10101;
    const L_MVL = 10200;
    const L_MVL_L = 10201;
    const M_MVL = 10300;
    const M_MVL_L = 10301;
    const SM_MVL = 10400;
    const SM_MVL_L = 10401;
    const S_MVL_MT = 10500;
    const S_MVL_MT_L = 10501;
    const S_MVL_AT = 10510;
    const SS_MVL = 10600;
    const SS_MVL_L = 10601;
    const TOWING = 10700;
    const TOWING_L = 10701;
    const L_ML = 10800;
    const L_ML_L = 10801;
    const L_ML_AT = 10810;
    const S_ML = 10900;
    const S_ML_L = 10901;
    const S_ML_AT = 10910;
    const MBL = 11000;
    const SL_MVL_2 = 20100;
    const L_MVL_2 = 20200;
    const M_MVL_2 = 20300;
    const S_MVL_2 = 20500;
    const TOWING_2 = 20700;
    const PL_MVL = 30200;
    const PM_MVL = 30300;
    const PSM_MVL = 30400;
    const PS_MVL_MT = 30500;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::SL_MVL => "大型特殊自動車免許",
            self::SL_MVL_L => "大型特殊自動車免許限定解除",
            self::L_MVL => "大型自動車免許",
            self::L_MVL_L => "大型自動車免許限定解除",
            self::M_MVL => "中型自動車免許",
            self::M_MVL_L => "中型自動車免許限定解除",
            self::SM_MVL => "準中型自動車免許",
            self::SM_MVL_L => "準中型自動車免許限定解除",
            self::S_MVL_MT => "普通MT自動車免許",
            self::S_MVL_MT_L => "普通MT自動車免許限定解除",
            self::S_MVL_AT => "AT限定普通自動車免許",
            self::SS_MVL => "小型特殊自動車免許",
            self::SS_MVL_L => "小型特殊自動車免許限定解除",
            self::TOWING => "牽引自動車免許",
            self::TOWING_L => "牽引自動車免許限定解除",
            self::L_ML => "大型二輪免許",
            self::L_ML_L => "大型二輪免許限定解除",
            self::L_ML_AT => "AT限定大型二輪免許",
            self::S_ML => "普通二輪免許",
            self::S_ML_L => "普通二輪免許限定解除",
            self::S_ML_AT => "AT限定普通二輪免許",
            self::MBL => "原付免許",
            self::SL_MVL_2 => "大型特殊第二種免許",
            self::L_MVL_2 => "大型第二種免許",
            self::M_MVL_2 => "中型第二種免許",
            self::S_MVL_2 => "普通第二種免許",
            self::TOWING_2 => "牽引第二種免許",
            self::PL_MVL => "大型自動車仮免許",
            self::PM_MVL => "中型自動車仮免許",
            self::PSM_MVL => "準中型自動車仮免許",
            self::PS_MVL_MT => "普通MT自動車仮免許",
        };
    }
}
