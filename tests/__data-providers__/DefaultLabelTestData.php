<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\DataProviders;

use Efortmeyer\Polar\Core\Defaults;

class DefaultLabelTestData
{
    public static function testCases()
    {
        return array_map(
            function ($char) {
                $propertyName = uniqid($char);
                $columnFormatter = Defaults::LABEL_FORMATTER;
                return [$propertyName, $columnFormatter($propertyName)];
            },
            range("a", "z")
        );
    }
}
