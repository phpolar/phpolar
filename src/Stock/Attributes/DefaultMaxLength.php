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
    public function __invoke(mixed $value = null): ValidationInterface
    {
        return new ValidationMaxLength($value, Defaults::MAX_LENGTH);
    }

    public function isValidator(): bool
    {
        return true;
    }
}
