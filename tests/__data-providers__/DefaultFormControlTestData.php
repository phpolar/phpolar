<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

use Phpolar\Phpolar\Stock\Attributes\Defaults;

class DefaultFormControlTestData
{
    public static function testCases()
    {
        return [
            [Defaults::FORM_CONTROL_TYPE],
        ];
    }
}
