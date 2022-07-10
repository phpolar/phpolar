<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Api\Attributes\AttributeInterface;
use Efortmeyer\Polar\Api\Validation\ValidationInterface;
use Efortmeyer\Polar\Core\Defaults;
use Efortmeyer\Polar\Stock\Validation\MaxLength as ValidationMaxLength;

/**
 * Use to validate that length of a field's value
 * is not over the default max length.
 */
class DefaultMaxLength implements AttributeInterface
{
    /**
     * The value to validate.
     *
     * @var mixed
     */
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __invoke(): ValidationInterface
    {
        return new ValidationMaxLength($this->value, Defaults::MAX_LENGTH);
    }
}
