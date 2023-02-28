<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Attribute;
use Phpolar\Phpolar\Core\AbstractPropertyValueExtractor;

/**
 * Provides support for configuring the max value of a property.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Max extends AbstractPropertyValueExtractor implements ValidatorInterface
{
    public function __construct(private int|float $max)
    {
    }

    public function isValid(): bool
    {
        return is_numeric($this->val) === true ? $this->val <= $this->max : true;
    }
}
