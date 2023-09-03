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
    public Attribute $labelAttribute;

    public Attribute $formControlTypeAttribute;

    public Attribute $columnAttribute;

    private ?Attribute $dateFormatAttribute;

    private ?Attribute $valueAttribute;

    /**
     * @var Attribute[]
     */
    public array $validatorAttributes = [];

    protected function __construct() {}

    public static function create(AttributeCollection $attributes): FieldMetadataConfig
    {
        $config = new self();
        $config->labelAttribute = $attributes->getLabelAttribute();
        $config->columnAttribute = $attributes->getColumnAttribute();
        $config->formControlTypeAttribute = $attributes->getFormControlAttribute();
        $config->valueAttribute = $attributes->getValueAttributeOrNull();
        $config->dateFormatAttribute = $attributes->getDateFormatAttributeOrNull();
        $config->validatorAttributes = $attributes->getValidatorAttributes();
        return $config;
    }

    public function getAttributeValueOrElse($value)
    {
        $valueAttribute = $this->valueAttribute;
        return $valueAttribute instanceof Attribute ? $valueAttribute() : $value;
    }

    public function getDateFormatOrEmptyString(): string
    {
        $dateFormatAttribute = $this->dateFormatAttribute;
        return $dateFormatAttribute instanceof Attribute ? (string) $dateFormatAttribute() : "";
    }

    /**
     * @return ValidationInterface[]
     */
    public function getValidators(): array
    {
        return array_map(fn (Attribute $attribute) => $attribute(), $this->validatorAttributes);
    }
}
