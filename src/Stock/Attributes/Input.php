<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Attribute as GlobalAttribute;
use Efortmeyer\Polar\Core\Attributes\Attribute;
use Efortmeyer\Polar\Core\Attributes\InputTypes;
use Efortmeyer\Polar\Core\Fields\DateField;
use Efortmeyer\Polar\Core\Fields\NumberField;
use Efortmeyer\Polar\Core\Fields\TextAreaField;
use Efortmeyer\Polar\Core\Fields\TextField;

#[GlobalAttribute(GlobalAttribute::TARGET_PROPERTY)]
final class Input extends Attribute
{
    protected readonly string $type;

    public function __construct(InputTypes | string $type)
    {
        $this->type = $type === "date" ? InputTypes::Date->value : (is_string($type) === true ? $type : $type->value);
    }

    public function __invoke(): string
    {
        return $this->type;
    }

    public function isFormControl(): bool
    {
        return true;
    }

    public function getFieldClassName(): string
    {
        $fieldClassNameMap = [
            InputTypes::Text->value => TextField::class,
            InputTypes::Textarea->value => TextAreaField::class,
            InputTypes::Number->value => NumberField::class,
            InputTypes::Date->value => DateField::class,
        ];
        return $fieldClassNameMap[$this->type];
    }
}
