<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes\Config;

use Phpolar\Phpolar\Api\Attributes\Config\Key;
use Phpolar\Phpolar\Stock\Attributes\AutomaticDateValue;

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
