<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static WAITING_FOR_APPLICATION()
 * @method static static SCHEDULED_WAITING()
 * @method static static PENDING()
 * @method static static COMPLETED()
 * @method static static APPROVED()
 * @method static static ATTENDED()
 * @method static static ABSENT()
 */
final class LessonAttendStatus extends Enum
{
    const WAITING_FOR_APPLICATION = 0;
    const SCHEDULED_WAITING = 1;
    const PENDING = 2;
    const COMPLETED = 3;
    const APPROVED = 4;

    const ATTENDED = 0;
    const ABSENT = 1;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::ATTENDED => "出席",
            self::ABSENT => "不在 のどれか",
        };
    }
}
