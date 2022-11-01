<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static SYS_ADMINISTRATOR()
 * @method static static CLERK_ONE()
 * @method static static CLERK_TWO()
 * @method static static APTITUDE_TESTER()
 * @method static static INSTRUCTOR()
 * @method static static EXAMINER()
 * @method static static SUB_ADMINISTRATOR()
 * @method static static ADMINISTRATOR()
 */
final class SchoolStaffRole extends Enum
{
    const SYS_ADMINISTRATOR = 1;
    const CLERK_ONE = 2;
    const CLERK_TWO = 4;
    const APTITUDE_TESTER = 8;
    const INSTRUCTOR = 16;
    const EXAMINER = 32;
    const SUB_ADMINISTRATOR = 64;
    const ADMINISTRATOR = 128;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::SYS_ADMINISTRATOR => "システム管理者",
            self::CLERK_ONE => "事務員1",
            self::CLERK_TWO => "事務員2",
            self::APTITUDE_TESTER => "適性検査員",
            self::INSTRUCTOR => "指導員",
            self::EXAMINER => "検定員",
            self::SUB_ADMINISTRATOR => "副管理者",
            self::ADMINISTRATOR => "管理者",
        };
    }
}
