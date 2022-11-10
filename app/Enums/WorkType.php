<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class WorkType extends Enum
{
    const WORKTYPE_REST = 9000001;
    const WORKTYPE_ANNUAL_LEAVE = 9000002;
    const WORKTYPE_SPECIAL_HOLIDAY = 9000005;
    const WORKTYPE_WEEKLY_HOLIDAY = 9000006;
    const WORKTYPE_CHILCARE_LEAVE = 9000010;
    const WORKTYPE_ABSENTEEISM = 9000011;
    const WORKTYPE_LATE_ARRIVAL = 9000050;
    const WORKTYPE_BEHIND_TIME = 9000051;
    const WORKTYPE_COMPANY_USE = 9000053;
    const WORKTYPE_PRIVATE = 9000056;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::WORKTYPE_REST => "振休",
            self::WORKTYPE_ANNUAL_LEAVE => "年休",
            self::WORKTYPE_SPECIAL_HOLIDAY => "特休",
            self::WORKTYPE_WEEKLY_HOLIDAY => "週休",
            self::WORKTYPE_CHILCARE_LEAVE => "育休",
            self::WORKTYPE_ABSENTEEISM => "欠勤",
            self::WORKTYPE_LATE_ARRIVAL => "遅出",
            self::WORKTYPE_BEHIND_TIME => "遅刻",
            self::WORKTYPE_COMPANY_USE => "社用",
            self::WORKTYPE_PRIVATE => "私用",
        };
    }
}
