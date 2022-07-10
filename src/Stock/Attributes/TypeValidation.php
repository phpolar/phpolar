<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Api\Attributes\AttributeInterface;
use Efortmeyer\Polar\Api\Validation\ValidationInterface;
use Efortmeyer\Polar\Stock\Validation\TypeValidation as ValidationTypeValidation;

/**
 * Configures validation of the type of a property's value.
 */
final class TypeValidation implements AttributeInterface
{
    /**
     * The value to validate.
     *
     * @var mixed
     */
    private $value;

    /**
     * The expected type of the value.
     *
     * @var string
     */
    private $type;

    public function __construct($value, string $type)
    {
        $this->value = $value;
        $this->type = $type;
    }

    public function __invoke(): ValidationInterface
    {
        return new ValidationTypeValidation($this->value, $this->type);
    }
}
