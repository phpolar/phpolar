<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

use Phpolar\Phpolar\Stock\Attributes\Defaults;

class DefaultColumnTestData
{
    public static function testCases()
    {
        return array_map(
            function ($char) {
                $propertyName = uniqid($char);
                $columnFormatter = Defaults::COLUMN_FORMATTER;
                return [$propertyName, $columnFormatter($propertyName)];
            },
            range("a", "z")
        );
    }
}
