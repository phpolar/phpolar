<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

use Phpolar\Phpolar\Stock\Attributes\Defaults;

class DefaultDateFormatTestData
{
    public static function testCases()
    {
        return [
            [Defaults::DATE_FORMAT],
        ];
    }
}
