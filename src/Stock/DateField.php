<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock;

/**
 * Represents a date field.
 */
class DateField extends Field
{
    public string $formControlType = "datetime-local";
}
