<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Validation;

use Efortmeyer\Polar\Api\Validation\ValidationInterface;
use Efortmeyer\Polar\Stock\Attributes\Messages;
use Serializable;

/**
 * Provides a way to validate a property's type.
 */
class TypeValidation implements ValidationInterface
{
    private string $errorMessage = "";

    private bool $typeIsValid = false;

    public function __construct(public mixed $value, private string $type)
    {
        $this->setState();
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function isValid(): bool
    {
        return $this->typeIsValid;
    }

    private function setState()
    {
        // the functions will be used to validate their corresponding type
        $typeCheckMap = [
            ScalarTypes::String->value    => "is_string",
            ScalarTypes::Integer->value   => "is_int",
            ScalarTypes::Float->value     => "is_float",
            ScalarTypes::Double->value    => "is_float",
            ScalarTypes::Bool->value      => "is_bool",
            ScalarTypes::Null->value      => fn ($value) => $value === null,
            Serializable::class           => fn ($value) => is_subclass_of($value, Serializable::class) === true,
        ];
        if (isset($typeCheckMap[$this->type]) === true) {
            $this->typeIsValid = $typeCheckMap[$this->type]($this->value);
            if ($this->typeIsValid === false) {
                $this->errorMessage = Messages::InvalidType->value;
            }
        } else {
            $this->typeIsValid = false;
            $this->errorMessage = Messages::UknownType->value;
        }
    }
}
