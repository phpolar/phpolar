<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Fields;

/**
 * Represents a date field with and automatically
 * generated value.
 */
final class AutomaticDateField extends FieldMetadata
{
    public string $formControlType = "datetime-local";
}
