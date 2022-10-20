<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

class Role extends Enum
{
    const CLERK_1 = 1;
    const CLERK_2 = 2;
    const APTITUDE_TESTER = 3;
    const INSTRUCTOR = 4;
    const EXAMINER = 5;
    const SUB_ADMINISTRATOR = 6;
    const ADMINISTRATOR = 7;
}