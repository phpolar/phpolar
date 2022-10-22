<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Core\Attributes\Attribute;
use Efortmeyer\Polar\Api\Validation\ValidationInterface;
use Efortmeyer\Polar\Stock\Validation\TypeValidation as ValidationTypeValidation;

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
