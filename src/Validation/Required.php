<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Attribute;
use ReflectionProperty;

use Phpolar\Phpolar\PropertyValueSetterInterface;

/**
 * Provides support for marking a property as requiring a value.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Required implements ValidatorInterface, PropertyValueSetterInterface
{
    protected mixed $val;

    public function isValid(): bool
    {
        return $this->val !== "" && $this->val !== null;
    }

    public function withPropVal(ReflectionProperty $prop, object $obj): static
    {
        $copy = clone $this;
        $copy->val = $prop->isInitialized($obj) === true ? $prop->getValue($obj) : null;
        return $copy;
    }
}
