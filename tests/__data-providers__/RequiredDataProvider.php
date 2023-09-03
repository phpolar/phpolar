<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

final class RequiredDataProvider
{
    public function nonEmptyVals(): mixed
    {
        return [
            [random_int(0, PHP_INT_MAX)],
            [uniqid()],
            [true],
            [false],
        ];
    }

    public function emptyVals(): mixed
    {
        return [
            [null],
            [""]
        ];
    }
}
