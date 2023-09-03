<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes\Config;

use Efortmeyer\Polar\Api\Attributes\Config\Key;
use Efortmeyer\Polar\Stock\Attributes\AutomaticDateValue;

final class AutomaticDateValueKey extends Key
{
    public function getKey(): string
    {
        return AutomaticDateValue::class;
    }

    public function __toString(): string
    {
        return AutomaticDateValue::class;
    }
}