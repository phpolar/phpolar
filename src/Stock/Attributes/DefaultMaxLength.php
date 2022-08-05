<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Core\Attributes\Attribute;
use Efortmeyer\Polar\Api\Validation\ValidationInterface;
use Efortmeyer\Polar\Stock\Validation\MaxLength as ValidationMaxLength;

/**
 * Use to validate that length of a field's value
 * is not over the default max length.
 */
class DefaultMaxLength extends Attribute
{
    private mixed $value;

    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function __invoke(): ValidationInterface
    {
        return new ValidationMaxLength($this->value, Defaults::MAX_LENGTH);
    }

    public function isValidator(): bool
    {
        return true;
    }
}
