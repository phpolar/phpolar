<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Api\Attributes\AttributeInterface;
use Efortmeyer\Polar\Stock\Validation\Noop;

/**
 * Provides `noop` validation.
 */
final class NoopValidate implements AttributeInterface
{
    public function __invoke(): Noop
    {
        return new Noop();
    }
}
