<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

final class MinDataProvider
{
    public const MIN = 5;

    public static function numberBelowMin(): array
    {
        return [
            [random_int(PHP_INT_MIN, (int) self::MIN - 1)],
            [self::MIN - 1E-2],
        ];
    }

    public static function numberAboveMin(): array
    {
        return [
            [random_int((int) self::MIN, PHP_INT_MAX)],
            [self::MIN + 1E-2],
            [self::MIN],
            [(float) self::MIN],
        ];
    }
}
