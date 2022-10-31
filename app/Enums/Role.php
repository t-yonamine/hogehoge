<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

class Role extends Enum
{
    const SYS_ADMINISTRATOR = 1;
    const CLERK_1 = 2;
    const CLERK_2 = 4;
    const APTITUDE_TESTER = 8;
    const INSTRUCTOR = 16;
    const EXAMINER = 32;
    const SUB_ADMINISTRATOR = 64;
    const ADMINISTRATOR = 128;
    const STAFF_MANAGER = 2;

    /**
     * Get the description for an enum value
     *
     * @param  mixed  $value
     * @return string
     */
    public static function getRole($value): string
    {
        if ($value === self::SYS_ADMINISTRATOR) {
            return 'システム管理者';
        }
        if ($value === self::CLERK_1) {
            return '事務員1';
        }
        if ($value === self::CLERK_2) {
            return '事務員2';
        }
        if ($value === self::APTITUDE_TESTER) {
            return '適性検査員';
        }
        if ($value === self::INSTRUCTOR) {
            return '指導員';
        }
        if ($value === self::EXAMINER) {
            return '検定員';
        }
        if ($value === self::SUB_ADMINISTRATOR) {
            return '副管理者';
        }
        if ($value === self::ADMINISTRATOR) {
            return '管理者';
        }

        throw new \InvalidArgumentException("unknown user type: {$value}");
    }
}
