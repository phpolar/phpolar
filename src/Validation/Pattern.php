<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Attribute;

use Phpolar\Phpolar\AbstractPropertyValueExtractor;

/**
 * Provides support for configuring the expected pattern of a property.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Pattern extends AbstractPropertyValueExtractor implements ValidatorInterface
{
    public function __construct(private string $pattern)
    {
    }

    public function isValid(): bool
    {
        return is_string($this->val) && preg_match($this->pattern, $this->val) === 1;
    }
}
