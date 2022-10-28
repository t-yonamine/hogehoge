<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

class Role extends Enum
{
    const SYS_ADMINISTRATOR = 1;
    const CLERK_1 = 2;
    const CLERK_2 = 4;
    const APTITUDE_TESTER = 8;
    const INSTRUCTOR = 16;
    const EXAMINER = 32;
    const SUB_ADMINISTRATOR = 64;
    const ADMINISTRATOR = 128;
    const STAFF_MANAGER = 2;
}