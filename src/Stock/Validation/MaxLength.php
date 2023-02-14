<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Validation;

use Phpolar\Phpolar\Api\Validation\ValidationInterface;
use Phpolar\Phpolar\Stock\Attributes\Messages;

/**
 * Provides a way to validate the maximum length of a field.
 */
class MaxLength implements ValidationInterface
{
    private string $errorMessage = "";

    public function __construct(public readonly mixed $value, private readonly int $maxLength = PHP_INT_MAX)
    {
        if ($this->isValid() === false) {
            $this->handleError();
        }
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function isValid(): bool
    {
        return $this->isOversized() === false;
    }

    private function isOversized(): bool
    {
        return (strlen(is_scalar($this->value ?? "") === true ? (string) $this->value : "") > $this->maxLength);
    }

    private function handleError(): void
    {
        $this->errorMessage = Messages::OversizedValue->value;
    }
}
