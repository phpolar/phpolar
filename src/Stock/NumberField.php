<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock;

/**
 * Represents a number field.
 */
final class NumberField extends Field
{
    /**
     * @var string
     */
    public $formControlType = "number";
}
