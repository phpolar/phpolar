<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Exception;

use RuntimeException;

/**
 * Exception thrown if an invalid input type is cast to string
 */
final class InvalidInputTypeCastToStringException extends RuntimeException
{
    /**
     * {@inheritDoc}
     * @var string
     */
    protected $message = "Attempting to cast an invalid input type to string.";
}
