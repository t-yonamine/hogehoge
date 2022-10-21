<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

class StageType extends Enum
{
    const STAGE_1 = 1; // 第1段階
    const STAGE_2 = 2; // 第2段階
}
