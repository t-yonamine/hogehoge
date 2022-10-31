<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static SYS_ADMINISTRATOR()
 * @method static static MANAGER()
 */
final class StaffRole extends Enum
{
    const SYS_ADMINISTRATOR = 1;
    const MANAGER = 2;

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
            self::MANAGER => "担当者",
        };
    }
}
