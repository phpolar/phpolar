<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\Validation\ErrorMessages;
use Phpolar\Phpolar\Validation\Exception\ValidatorWithNoErrorMessageException;
use Phpolar\Phpolar\Validation\MaxLength;
use Phpolar\Phpolar\Validation\MinLength;
use Phpolar\Phpolar\Validation\ValidationErrorInterface;
use Phpolar\Phpolar\Validation\ValidatorInterface;

/**
 * Represents a validation error
 */
final class ValidationError implements ValidationErrorInterface
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function getMessage(): string
    {
        $validatorClassName = get_class($this->validator);
        return match ($validatorClassName) {
            Max::class => ErrorMessages::Max->value,
            MaxLength::class => ErrorMessages::MaxLength->value,
            Min::class => ErrorMessages::Min->value,
            MinLength::class => ErrorMessages::MinLength->value,
            Pattern::class => ErrorMessages::Pattern->value,
            Required::class => ErrorMessages::Required->value,
            default => throw new ValidatorWithNoErrorMessageException($validatorClassName)
        };
    }
}
