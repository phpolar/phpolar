<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Core\Attributes\Attribute;
use Efortmeyer\Polar\Stock\Validation\Noop;

/**
 * Provides `noop` validation.
 */
final class NoopValidate extends Attribute
{
    public function __invoke(): Noop
    {
        return new Noop();
    }

    public function isValidator(): bool
    {
        return true;
    }
}
