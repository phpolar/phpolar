<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Attribute;

use Phpolar\Phpolar\Core\AbstractPropertyValueExtractor;

/**
 * Provides support for configuring the min value of a property.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Min extends AbstractPropertyValueExtractor implements ValidatorInterface
{
    public function __construct(private int|float $min)
    {
    }

    public function isValid(): bool
    {
        return $this->min <= match (true) {
            is_int($this->val) || is_float($this->val) => $this->val,
            default => $this->min,
        };
    }
}
