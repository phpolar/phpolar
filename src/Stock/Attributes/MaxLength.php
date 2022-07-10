<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Api\Attributes\AttributeInterface;
use Efortmeyer\Polar\Api\Validation\ValidationInterface;
use Efortmeyer\Polar\Stock\Validation\MaxLength as ValidationMaxLength;

/**
 * Configures the max length of a property's value.
 */
final class MaxLength implements AttributeInterface
{
    /**
     * The value to validate.
     *
     * @var mixed
     */
    private $value;

    /**
     * @var integer
     */
    private $maxLength;

    public function __construct($value, int $maxLength)
    {
        $this->value = $value;
        $this->maxLength = $maxLength;
    }

    public function __invoke(): ValidationInterface
    {
        return new ValidationMaxLength($this->value, $this->maxLength);
    }
}
