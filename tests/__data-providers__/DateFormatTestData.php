<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

use DateTime;

class DateFormatTestData
{
    private const TEST_DATE_FORMATS = [
        DateTime::RSS,
        DateTime::W3C,
        DateTime::ATOM,
        DateTime::COOKIE,
    ];

    public static function testCases()
    {
        return array_map(
            fn (string $format) => [$format],
            self::TEST_DATE_FORMATS
        );
    }
}
