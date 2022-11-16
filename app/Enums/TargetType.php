<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static ATTENDANCE()
 * @method static static PERIOD()
 */
final class TargetType extends Enum
{
    const ATTENDANCE = 1;
    const PERIOD = 2;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::ATTENDANCE => "受講",
            self::PERIOD => "時限",
        };
    }
}
