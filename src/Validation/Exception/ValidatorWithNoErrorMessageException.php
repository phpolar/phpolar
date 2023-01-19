<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation\Exception;

use RuntimeException;

/**
 * An exception thrown if a validator does not have an error message configured
 */
final class ValidatorWithNoErrorMessageException extends RuntimeException
{
    public function __construct(string $targetClass)
    {
        $message = sprintf(
            "Have you forgotten to set up the error message for %s.",
            $targetClass,
        );
        parent::__construct($message);
    }
}
