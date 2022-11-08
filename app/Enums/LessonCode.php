<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static BEFORE()
 * @method static static FIRST_STAGE()
 * @method static static FIRST_TEST_WAIT()
 * @method static static PL_TESTWAIT()
 * @method static static SECOND_STAGE()
 * @method static static TEST_2_WAIT()
 * @method static static GRADUATION()
 */
final class LessonCode extends Enum
{
    const BEFORE = 1;
    const FIRST_STAGE = 2;
    const FIRST_TEST_WAIT = 3;
    const PL_TESTWAIT = 4;
    const SECOND_STAGE = 5;
    const TEST_2_WAIT = 6;
    const GRADUATION = 7;
}