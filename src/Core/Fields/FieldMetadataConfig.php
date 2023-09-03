<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Fields;

use Efortmeyer\Polar\Api\Validation\ValidationInterface;
use Efortmeyer\Polar\Core\Attributes\{
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
    public function getValidators(): array
    {
        return array_map(fn (Attribute $attribute) => $attribute(), $this->validatorAttributes);
    }
}
