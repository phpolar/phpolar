<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Core\Attributes\Attribute;
use Efortmeyer\Polar\Api\Validation\ValidationInterface;
use Efortmeyer\Polar\Stock\Validation\MaxLength as ValidationMaxLength;

/**
 * Configures the max length of a property's value.
 */
final class MaxLength extends Attribute
{
    public function __construct(private mixed $value, private int $maxLength)
    {
    }

    public function __invoke(): ValidationInterface
    {
        return new ValidationMaxLength($this->value, $this->maxLength);
    }

    public function isValidator(): bool
    {
        return true;
    }
}
