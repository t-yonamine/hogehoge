<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

class ResultType extends Enum
{
    const NG = 0;
    const OK = 1;
    const CANCEL = 2;
}