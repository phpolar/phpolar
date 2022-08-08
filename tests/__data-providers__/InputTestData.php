<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\DataProviders;

use Efortmeyer\Polar\Core\Attributes\InputTypes;

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
