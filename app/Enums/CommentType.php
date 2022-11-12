<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static WAITING_FOR_APPLICATION()
 * @method static static SCHEDULED_WAITING()
 */
final class CommentType extends Enum
{
    const ITEMS_TO_BE_SENT = 1;
    const OPTIONAL_ITEMS = 2;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::ITEMS_TO_BE_SENT => "申し送り事項",
            self::OPTIONAL_ITEMS => "任意項目",
        };
    }
}
