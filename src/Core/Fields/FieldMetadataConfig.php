<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Core\Fields;

use Phpolar\Phpolar\Api\Validation\ValidationInterface;
use Phpolar\Phpolar\Core\Attributes\{
    AttributeCollection,
    Attribute,
};

/**
 * Configures metadata for a field.
 */
class FieldMetadataConfig
{
    public Attribute $labelAttr;

    public Attribute $formControlTypeAttr;

    public Attribute $columnAttr;

    private ?Attribute $dateFormatAttr;

    private ?Attribute $valueAttr;

    /**
     * @var Attribute[]
     */
    public array $validatorAttributes = [];

    public function __construct(AttributeCollection $attributes)
    {
        $this->labelAttr = $attributes->getLabelAttribute();
        $this->columnAttr = $attributes->getColumnAttribute();
        $this->formControlTypeAttr = $attributes->getFormControlAttribute();
        $this->valueAttr = $attributes->getValueAttributeOrNull();
        $this->dateFormatAttr = $attributes->getDateFormatAttributeOrNull();
        $this->validatorAttributes = $attributes->getValidatorAttributes();
    }

    public function getAttributeValueOrElse($value)
    {
        $valueAttr = $this->valueAttr;
        return $valueAttr instanceof Attribute ? $valueAttr() : $value;
    }

    public function getDateFormatOrEmptyString(): string
    {
        $dateFormatAttr = $this->dateFormatAttr;
        return $dateFormatAttr instanceof Attribute ? (string) $dateFormatAttr() : "";
    }

    /**
     * @return ValidationInterface[]
     */
    public function getValidators(mixed $value): array
    {
        return array_map(fn (Attribute $attribute) => $attribute->withValue($value)->__invoke(), $this->validatorAttributes);
    }
}
