<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

use Phpolar\Phpolar\Core\Attributes\InputTypes;

class InputTestData
{
    public static function type()
    {
        return [
            [InputTypes::Text],
            [InputTypes::Textarea]
        ];
    }
}
