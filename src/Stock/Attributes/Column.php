<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Core\Attributes\Attribute;

/**
 * Configures a property's column name.
 */
final class Column extends Attribute
{
    public function __construct(private string $text)
    {
    }

    public function __invoke(): string
    {
        return $this->text;
    }

    public function isColumn(): bool
    {
        return true;
    }
}
