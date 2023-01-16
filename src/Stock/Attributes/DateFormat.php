<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Attribute as GlobalAttribute;
use Phpolar\Phpolar\Core\Attributes\Attribute;

/**
 * Configures the format a DateTime property.
 *
 * The given format will be used when the
 * DateTime object is converted to a string.
 */
#[GlobalAttribute(GlobalAttribute::TARGET_PROPERTY)]
final class DateFormat extends Attribute
{
    public function __construct(private readonly string $dateFormat)
    {
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
