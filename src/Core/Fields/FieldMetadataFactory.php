<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Fields;

use Efortmeyer\Polar\Core\Attributes\AttributeCollection;

final class FieldMetadataFactory
{
    /**
     * @var FieldMetadata
     */
    private $field;

    /**
     * @var FieldMetadataConfig
     */
    private $config;

    private function __construct(FieldMetadata $field, AttributeCollection $attributes)
    {
        $this->field = $field;
        $this->config = FieldMetadataConfig::create($attributes);
    }

    /**
     * Gets a factory instance.
     *
     * The purpose of this method is
     * for the factory to only be created
     * by a FieldMetadata instance.  A
     * FieldMetadata instance can only
     * be created by this factory.
     *
     * To create FieldMetadata,
     * createFactory -> create field
     */
    public static function getInstance(FieldMetadata $field, AttributeCollection $attributes): FieldMetadataFactory
    {
        return new self($field, $attributes);
    }

    /**
     * Create a Field.
     */
    public function create(string $propertyName, mixed $value): FieldMetadata
    {
        $formControlAttribute = $this->config->formControlTypeAttribute;
        $labelAttribute = $this->config->labelAttribute;
        $columnAttribute = $this->config->columnAttribute;

        $this->field->propertyName = $propertyName;
        $this->field->value = $this->config->getAttributeValueOrElse($value);
        $this->field->dateFormat = $this->config->getDateFormatOrEmptyString();
        $this->field->validators = $this->config->getValidators();
        $this->field->label = (string) $labelAttribute();
        $this->field->column = (string) $columnAttribute();
        $this->field->formControlType = (string) $formControlAttribute();

        return $this->field;
    }
}
