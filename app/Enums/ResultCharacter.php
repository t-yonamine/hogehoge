<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static A()
 * @method static static B()
 * @method static static C()
 * @method static static D()
 * @method static static DMINUS()
 * @method static static E()
 * @method static static EMINUS()
 */
final class ResultCharacter extends Enum
{
    const A = 'A';
    const B = 'B';
    const C = 'C';
    const D = 'D';
    const DMINUS = 'D-';
    const E = 'E';
    const EMINUS = 'E-';
}
