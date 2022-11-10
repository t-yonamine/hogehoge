<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static LICENSE_TYPE()
 * @method static static LESSON_STS()
 */
final class CodeName extends Enum
{
    const LICENSE_TYPE = "license_type";
    const LESSON_STS = "lesson_sts";
    const PERIODS = "gperiods";
}
