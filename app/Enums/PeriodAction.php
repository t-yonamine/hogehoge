<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static UPDATE_WORK
 * @method static static REDIRECT_LINK
 */
final class PeriodAction extends Enum
{
    const UPDATE_WORK = 'UPDATE_WORK';
    const REDIRECT_LINK = 'REDIRECT_LINK';
    const UPDATE_LESSON = 'UPDATE_LESSON';
}
