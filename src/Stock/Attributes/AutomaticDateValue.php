<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Core\Attributes\Attribute;

use DateTimeImmutable;
use Efortmeyer\Polar\Core\Attributes\InputTypes;
use Efortmeyer\Polar\Core\Fields\AutomaticDateField;

/**
 * Use when an automatic date value should be used.
 *
 * The value will be set to the current date.
 */
final class AutomaticDateValue extends Attribute
{
    public string $type = InputTypes::DATE;

    public function __invoke(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    public function isFormControl(): bool
    {
        return true;
    }

    public function isAutomaticDateInput(): bool
    {
        return true;
    }

    public function getFieldClassName(): string
    {
        return AutomaticDateField::class;
    }
}
