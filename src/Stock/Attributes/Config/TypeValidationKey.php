<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes\Config;

use Efortmeyer\Polar\Api\Attributes\Config\Key;
use Efortmeyer\Polar\Stock\Attributes\TypeValidation;

final class TypeValidationKey extends Key
{
    public function getKey(): string
    {
        return TypeValidation::class;
    }

    public function __toString()
    {
        return TypeValidation::class;
    }
}
