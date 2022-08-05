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
    private mixed $value;

    private string $type;

    public function __construct(mixed $value, string $type)
    {
        $this->value = $value;
        $this->type = $type;
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
