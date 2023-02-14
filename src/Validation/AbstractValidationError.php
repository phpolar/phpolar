<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\Core\Validation\DefaultErrorMessages;
use Phpolar\Phpolar\Core\Validation\Exception\ValidatorWithNoErrorMessageException;

/**
 * Provides a way to get error messages from
 * validators with errors.
 */
abstract class AbstractValidationError
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * Return the error message.
     *
     * @api
     */
    public function getMessage(): string
    {
        $validatorClassName = get_class($this->validator);
        return match ($validatorClassName) {
            Max::class => DefaultErrorMessages::Max->value,
            MaxLength::class => DefaultErrorMessages::MaxLength->value,
            Min::class => DefaultErrorMessages::Min->value,
            MinLength::class => DefaultErrorMessages::MinLength->value,
            Pattern::class => DefaultErrorMessages::Pattern->value,
            Required::class => DefaultErrorMessages::Required->value,
            default => throw new ValidatorWithNoErrorMessageException($validatorClassName)
        };
    }
}
