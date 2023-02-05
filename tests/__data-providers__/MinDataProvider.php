<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

final class MinDataProvider
{
    public const MIN = 5;

    public function numberBelowMin(): array
    {
        return [[random_int(PHP_INT_MIN, (int) self::MIN - 1)]];
    }

    public function numberAboveMin(): array
    {
        return [[random_int((int) self::MIN, PHP_INT_MAX)]];
    }
}
