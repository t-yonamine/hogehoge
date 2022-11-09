<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static CONFIRM()
 * @method static static APPROVAL()
 * @method static static TEST()
 * @method static static APPLY_APPRO()
 * @method static static RESULT_APPRO()
 */
final class ConfgInformationType extends Enum
{
    const CONFIRM = 1;
    const APPROVAL = 2;
    const TEST = 3;
    const APPLY_APPRO = 4;
    const RESULT_APPRO = 5;

    /**
     * Get the description for an enum value
     *
     * @param  mixed $value
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::CONFIRM => "確認",
            self::APPROVAL => "承認",
            self::TEST => "検査",
            self::APPLY_APPRO => "申込承認",
            self::RESULT_APPRO => "結果承認",
        };
    }
}
