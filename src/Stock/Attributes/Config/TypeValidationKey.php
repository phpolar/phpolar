<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes\Config;

use Phpolar\Phpolar\Api\Attributes\Config\Key;
use Phpolar\Phpolar\Stock\Attributes\TypeValidation;

final class TypeValidationKey extends Key
{
    public function getKey(): string
    {
        return TypeValidation::class;
    }

    public function __toString(): string
    {
        return TypeValidation::class;
    }
}
