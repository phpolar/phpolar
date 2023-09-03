<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Attribute as GlobalAttribute;
use Efortmeyer\Polar\Core\Attributes\Attribute;

/**
 * Configures the form label text of a property.
 */
#[GlobalAttribute()]
class Label extends Attribute
{
    public function __construct(private readonly string $labelText)
    {
    }

    public function __invoke(): string
    {
        return $this->labelText;
    }

    public function isLabel(): bool
    {
        return true;
    }
}
