<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

class LessonAttendStatus extends Enum
{
    const WAITING_FOR_APPLICATION = 0;
    const SCHEDULED_WAITING = 1;
    const PENDING = 2;
    const COMPLETED = 3;
    const APPROVED = 4;
}