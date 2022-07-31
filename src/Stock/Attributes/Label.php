<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Core\Attributes\Attribute;

/**
 * Configures the form label text of a property.
 */
class Label extends Attribute
{
    private string $labelText;

    public function __construct(string $labelText)
    {
        $this->labelText = $labelText;
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
