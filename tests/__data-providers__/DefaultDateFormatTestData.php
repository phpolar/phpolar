<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\DataProviders;

use Efortmeyer\Polar\Core\Defaults;

class DefaultDateFormatTestData
{
    public static function testCases()
    {
        return [
            [Defaults::DATE_FORMAT],
        ];
    }
}
