<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Core\Attributes\Attribute;

/**
 * Converts a DateTime property to a string with the
 * default format.
 */
class DefaultDateFormat extends Attribute
{
    public function __invoke(): string
    {
        return Defaults::DATE_FORMAT;
    }

    public function isDateFormat(): bool
    {
        return true;
    }
}
