<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static IMPLEMENTED()
 * @method static static CANCELED_DUE_TO_PRE_CHECK()
 * @method static static OTHER_CANCELED()
 */
final class CancelType extends Enum
{
    const IMPLEMENTED = 0;
    const CANCELED_DUE_TO_PRE_CHECK = 1;
    const OTHER_CANCELED = 2;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::IMPLEMENTED => "実施した",
            self::CANCELED_DUE_TO_PRE_CHECK => "事前チェックで中止",
            self::OTHER_CANCELED => "その他中止",
        };
    }
}
