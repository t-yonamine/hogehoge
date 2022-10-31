<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static WAITING_FOR_APPLICATION()
 * @method static static SCHEDULED_WAITING()
 * @method static static PENDING()
 * @method static static COMPLETED()
 * @method static static APPROVED()
 */
final class LessonAttendStatus extends Enum
{
    const WAITING_FOR_APPLICATION = 0;
    const SCHEDULED_WAITING = 1;
    const PENDING = 2;
    const COMPLETED = 3;
    const APPROVED = 4;
}
