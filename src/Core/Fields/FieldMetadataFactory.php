<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Core\Fields;

final class FieldMetadataFactory
{
    public function __construct(private readonly FieldMetadata $field, private readonly FieldMetadataConfig $config)
    {
    }

    /**
     * Create a Field.
     */
    public function create(string $propertyName, mixed $value): FieldMetadata
    {
        $formControlAttribute = $this->config->formControlTypeAttr;
        $labelAttribute = $this->config->labelAttr;
        $columnAttribute = $this->config->columnAttr;

        $this->field->propertyName = $propertyName;
        $this->field->value = $this->config->getAttributeValueOrElse($value);
        $this->field->dateFormat = $this->config->getDateFormatOrEmptyString();
        $this->field->validators = $this->config->getValidators($value);
        $this->field->label = (string) $labelAttribute();
        $this->field->column = (string) $columnAttribute();
        $this->field->formControlType = (string) $formControlAttribute();

        return $this->field;
    }
}
