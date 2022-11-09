<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static WAITING()
 * @method static static WAITING_CONFIRMATION()
 * @method static static CONFIRMED()
 */
final class ConfirmationRecsStatus extends Enum
{

    const WAITING = 0;
    const WAITING_CONFIRMATION = 1;
    const CONFIRMED = 2;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::WAITING => "待機",
            self::WAITING_CONFIRMATION => "確認待ち",
            self::CONFIRMED => "確認済み",
        };
    }
}
