<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Api\Attributes\AttributeInterface;
use Efortmeyer\Polar\Core\Defaults;

/**
 * Converts a DateTime property to a string with the
 * default format.
 */
class DefaultDateFormat implements AttributeInterface
{
    public function __invoke(): string
    {
        return Defaults::DATE_FORMAT;
    }
}
