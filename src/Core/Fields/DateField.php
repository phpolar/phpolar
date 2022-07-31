<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Fields;

/**
 * Represents a date field.
 */
class DateField extends FieldMetadata
{
    public string $formControlType = "datetime-local";
}
