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
    /**
     * @var mixed
     */
    public $value;

    private string $type;

    private string $errorMessage = "";

    private bool $typeIsValid = false;

    public function __construct($value, string $type)
    {
        $this->value = $value;
        $this->type = $type;
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
            ScalarTypes::STRING    => "is_string",
            ScalarTypes::INTEGER   => "is_int",
            ScalarTypes::FLOAT     => "is_float",
            ScalarTypes::DOUBLE    => "is_float",
            ScalarTypes::BOOL      => "is_bool",
            ScalarTypes::NULL      => fn ($value) => $value === null,
            Serializable::class    => fn ($value) => is_subclass_of($value, Serializable::class) === true,
        ];
        if (isset($typeCheckMap[$this->type]) === true) {
            $this->typeIsValid = $typeCheckMap[$this->type]($this->value);
            if ($this->typeIsValid === false) {
                $this->errorMessage = Messages::INVALID_TYPE;
            }
        } else {
            $this->typeIsValid = false;
            $this->errorMessage = Messages::UKNOWN_TYPE;
        }
    }
}
