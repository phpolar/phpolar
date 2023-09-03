<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

final class MaxDataProvider
{
    public const MAX = 5e10;

    public function numberBelowMax(): array
    {
        return [[random_int(PHP_INT_MIN, (int) self::MAX)]];
    }

    public function numberAboveMax(): array
    {
        return [[random_int((int) self::MAX + 1, PHP_INT_MAX)]];
    }
}
