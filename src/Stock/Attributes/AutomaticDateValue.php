<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Api\Attributes\AttributeInterface;
use DateTimeImmutable;

/**
 * Use when an automatic date value should be used.
 *
 * The value will be set to the current date.
 */
final class AutomaticDateValue implements AttributeInterface
{
    public function __invoke()
    {
        return new DateTimeImmutable();
    }
}
