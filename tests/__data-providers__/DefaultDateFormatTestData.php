<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\DataProviders;

use Efortmeyer\Polar\Stock\Attributes\Defaults;

class DefaultDateFormatTestData
{
    public static function testCases()
    {
        return [
            [Defaults::DATE_FORMAT],
        ];
    }
}
