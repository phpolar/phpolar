<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Attribute;
use Phpolar\Phpolar\Core\AbstractPropertyValueExtractor;

/**
 * Provides support for configuring the minimum length of a property.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class MinLength extends AbstractPropertyValueExtractor implements ValidatorInterface
{
    public function __construct(private int $minLen)
    {
    }

    public function isValid(): bool
    {
        return $this->minLen <= match (true) {
            is_string($this->val) => mb_strlen($this->val),
            is_int($this->val) => strlen(strval(abs($this->val))),
            default => $this->minLen,
        };
    }
}
