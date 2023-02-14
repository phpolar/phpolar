<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Phpolar\Phpolar\Core\Attributes\Attribute;

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
