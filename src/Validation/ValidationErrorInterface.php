<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

/**
 * Provides a way to get error messages from
 * validators with errors.
 */
interface ValidationErrorInterface
{
    /**
     * Use to retrieve the error message for display.
     */
    public function getMessage(): string;
}
