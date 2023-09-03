<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\DataProviders;

class InputTestData
{
    public static function type()
    {
        return [
            [uniqid()],
            [uniqid()]
        ];
    }
}
