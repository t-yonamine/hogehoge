<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OPEN_MODAL()
 * @method static static LICENSE_CONFIRM()
 * @method static static DELETE_APPPLICATION()
 * @method static static TOP_BUTTON()
 * @method static static UP_BUTTON()
 * @method static static DOWN_BUTTON()
 * @method static static END_BUTTON()
 */
final class LessonAttendOption extends Enum
{
    const UP_RECORD = 1;
    const DOWN_RECORD = -1;
    const OPEN_MODAL = 'OPEN_MODAL';
    const LICENSE_CONFIRM = 'LICENSE_CONFIRM';
    const DELETE_APPPLICATION = 'DELETE_APPPLICATION';
    const TOP_BUTTON = 'TOP_BUTTON';
    const UP_BUTTON = 'UP_BUTTON';
    const DOWN_BUTTON = 'DOWN_BUTTON';
    const END_BUTTON = 'END_BUTTON';
}
