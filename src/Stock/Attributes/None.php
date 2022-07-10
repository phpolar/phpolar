<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Api\Attributes\AttributeInterface;

/**
 * Provides `noop` configuration.
 */
final class None implements AttributeInterface
{
    public function __invoke()
    {
    }
}
