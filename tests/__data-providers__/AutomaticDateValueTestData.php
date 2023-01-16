<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

use DateTimeImmutable;

class AutomaticDateValueTestData
{
    public static function testCases()
    {
        return [[new DateTimeImmutable()]];
    }
}
