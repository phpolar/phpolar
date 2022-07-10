<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Validation;

use Efortmeyer\Polar\Api\Validation\ValidationInterface;

use Efortmeyer\Polar\Core\Messages;

/**
 * Provides a way to validate the maximum length of a field.
 */
class MaxLength implements ValidationInterface
{
    /**
     * @var mixed
     */
    public $value;

    /**
     * @var integer
     */
    private $maxLength;

    /**
     * @var string
     */
    private $errorMessage = "";

    public function __construct($value, int $maxLength = PHP_INT_MAX)
    {
        $this->value = $value;
        $this->maxLength = $maxLength;

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
        return (strlen($this->value ?? "") > $this->maxLength);
    }

    private function handleError(): void
    {
        $this->errorMessage = Messages::OVERSIZED_VALUE;
    }
}
