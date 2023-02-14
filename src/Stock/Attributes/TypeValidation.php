<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Phpolar\Phpolar\Core\Attributes\Attribute;
use Phpolar\Phpolar\Api\Validation\ValidationInterface;
use Phpolar\Phpolar\Stock\Validation\TypeValidation as ValidationTypeValidation;

/**
 * Configures validation of the type of a property's value.
 */
final class TypeValidation extends Attribute
{
    public function __construct(protected mixed $value, private readonly string $type)
    {
    }

    public function __invoke(): ValidationInterface
    {
        return new ValidationTypeValidation($this->value, $this->type);
    }

    public function isValidator(): bool
    {
        return true;
    }
}
