<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Validation;

use Phpolar\Phpolar\Api\Validation\ValidationInterface;

/**
 * Does no validation.
 */
final class Noop implements ValidationInterface
{
    public function __construct()
    {
    }

    public function getErrorMessage(): string
    {
        return "";
    }

    public function isValid(): bool
    {
        return true;
    }
}
