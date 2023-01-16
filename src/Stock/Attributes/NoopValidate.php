<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Phpolar\Phpolar\Core\Attributes\Attribute;
use Phpolar\Phpolar\Stock\Validation\Noop;

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
