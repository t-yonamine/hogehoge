<?php

namespace App\Enums;

use Illuminate\Validation\Rules\Enum;

class LaType extends Enum
{
    const PRE_EXAMINATION = 2211;
    const GRADUATION = 2221;
    
    // La_type Effect measurement
    const EFF_MEAS_MIN = 2200;
    const EFF_MEAS_1 = 2210;
    const EFF_MEAS_1n = 2211;
    const EFF_MEAS_2 = 2220;
    const EFF_MEAS_2n = 2221;
    const EFF_MEAS_MAX = 2299;
}