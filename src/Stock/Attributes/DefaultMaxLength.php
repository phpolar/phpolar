<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Phpolar\Phpolar\Core\Attributes\Attribute;
use Phpolar\Phpolar\Api\Validation\ValidationInterface;
use Phpolar\Phpolar\Stock\Validation\MaxLength as ValidationMaxLength;

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
