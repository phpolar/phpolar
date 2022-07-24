<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock;

/**
 * Represents a date field with and automatically
 * generated value.
 */
final class AutomaticDateField extends Field
{
    public string $formControlType = "datetime-local";
}
