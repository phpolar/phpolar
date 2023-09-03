<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Attribute as GlobalAttribute;
use Efortmeyer\Polar\Core\Attributes\Attribute;

use DateTimeImmutable;
use Efortmeyer\Polar\Core\Attributes\InputTypes;
use Efortmeyer\Polar\Core\Fields\AutomaticDateField;

/**
 * Use when an automatic date value should be used.
 *
 * The value will be set to the current date.
 */
#[GlobalAttribute(GlobalAttribute::TARGET_PROPERTY)]
final class AutomaticDateValue extends Attribute
{
    public string $type;

    public function __construct()
    {
        $this->type = InputTypes::Date->value;
    }

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
