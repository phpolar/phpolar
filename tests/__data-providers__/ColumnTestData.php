<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\DataProviders;

class ColumnTestData
{
    public static function text()
    {
        return [
            [uniqid()],
            [uniqid()]
        ];
    }
}
