<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\Validation;

/**
 * Provides a way to validate a field.
 */
interface ValidationInterface
{
    /**
     * Returns the error message for an invalid field.
     *
     * @api
     */
    public function getErrorMessage(): string;

    /**
     * Determines if the field is invalid.
     *
     * @api
     */
    public function isValid(): bool;
}
