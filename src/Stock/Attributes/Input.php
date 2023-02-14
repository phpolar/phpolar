<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Attribute as GlobalAttribute;
use Phpolar\Phpolar\Core\Attributes\Attribute;
use Phpolar\Phpolar\Core\Attributes\InputTypes;
use Phpolar\Phpolar\Core\Fields\DateField;
use Phpolar\Phpolar\Core\Fields\NumberField;
use Phpolar\Phpolar\Core\Fields\TextAreaField;
use Phpolar\Phpolar\Core\Fields\TextField;

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
