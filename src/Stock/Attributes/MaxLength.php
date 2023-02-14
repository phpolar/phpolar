<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Attribute as GlobalAttribute;
use Phpolar\Phpolar\Core\Attributes\Attribute;
use Phpolar\Phpolar\Api\Validation\ValidationInterface;
use Phpolar\Phpolar\Stock\Validation\MaxLength as ValidationMaxLength;

/**
 * Configures the max length of a property's value.
 */
#[GlobalAttribute(GlobalAttribute::TARGET_PROPERTY)]
final class MaxLength extends Attribute
{
    public function __construct(private readonly int $maxLength)
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
