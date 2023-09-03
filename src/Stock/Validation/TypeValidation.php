<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Validation;

use Efortmeyer\Polar\Api\Validation\ValidationInterface;

use Efortmeyer\Polar\Core\Messages;

use Serializable;

/**
 * Provides a way to validate a property's type.
 */
final class TypeValidation implements ValidationInterface
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

    private function handleError(string $errorMessage): bool
    {
        $this->errorMessage = $errorMessage;
        return false;
    }

    private function setState(): void
    {
        switch ($this->type) {
            case ScalarTypes::STRING:
                $this->typeIsValid = is_string($this->value) === false ? $this->handleError(Messages::INVALID_TYPE) : true;
                break;
            case ScalarTypes::INTEGER:
                $this->typeIsValid = is_int($this->value) === false ? $this->handleError(Messages::INVALID_TYPE) : true;
                break;
            case ScalarTypes::FLOAT:
            case ScalarTypes::DOUBLE:
                $this->typeIsValid = is_float($this->value) === false ? $this->handleError(Messages::INVALID_TYPE) : true;
                break;
            case ScalarTypes::NULL:
                $this->typeIsValid = $this->value !== null ? $this->handleError(Messages::INVALID_TYPE) : true;
                break;
            case ScalarTypes::BOOL:
                $this->typeIsValid = is_bool($this->value) === false ? $this->handleError(Messages::INVALID_TYPE) : true;
                break;
            case Serializable::class:
                $this->typeIsValid = is_subclass_of($this->value, Serializable::class) === false ? $this->handleError(Messages::INVALID_TYPE) : true;
                break;
            default:
                $this->typeIsValid = $this->handleError(Messages::UKNOWN_TYPE);
        }
    }
}
