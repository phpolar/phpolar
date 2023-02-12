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
        return is_int($this->val) === true || is_float($this->val) === true ? $this->val >= $this->min : true;
    }
}
