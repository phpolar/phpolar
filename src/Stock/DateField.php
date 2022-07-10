<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock;

/**
 * Represents a date field.
 */
class DateField extends Field
{
    /**
     * @var string
     */
    public $formControlType = "datetime-local";
}
