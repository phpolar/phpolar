<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Core\Attributes\Attribute;

/**
 * Configures the format a DateTime property.
 *
 * The given format will be used when the
 * DateTime object is converted to a string.
 */
final class DateFormat extends Attribute
{
    private string $dateFormat;

    public function __construct(string $dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    public function __invoke(): string
    {
        return $this->dateFormat;
    }

    public function isDateFormat(): bool
    {
        return true;
    }
}
