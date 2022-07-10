<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\DataProviders;

use Efortmeyer\Polar\Core\Defaults;

class DefaultFormControlTestData
{
    public static function testCases()
    {
        return [
            [Defaults::FORM_CONTROL_TYPE],
        ];
    }
}
