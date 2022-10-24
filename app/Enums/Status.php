<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

class Status extends Enum
{
    const DISABLE = 0;
    const ENABLE = 1;
}
