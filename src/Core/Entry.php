<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core;

use Efortmeyer\Polar\Api\Attributes\Config\Collection as AttributeConfigCollection;
use Efortmeyer\Polar\Core\Attributes\AttributeCollection;
use Efortmeyer\Polar\Core\Fields\FieldMetadata;
use Efortmeyer\Polar\Core\Fields\FieldMetadataConfig;
use Efortmeyer\Polar\Core\Fields\FieldMetadataFactory;
use ReflectionAttribute;
use ReflectionObject;
use ReflectionProperty;
use RuntimeException;

/**
 * Represents an item in a collection or a row in a table.
 */
abstract class Entry
{
    /**
     * @var FieldMetadata[]
     */
    private readonly array $fields;

    /**
     * @throws RuntimeException
     */
    public function __construct(private readonly AttributeConfigCollection $attributeConfigMap, array $storedValues = [])
    {
        if (empty($storedValues) === false) {
            $this->setValues($storedValues);
        }

        /**
         * The annotations/attributes on the entry's properties
         * are used to configure the fields.
         */
        $this->fields = array_map(
            $this->createField(...),
            (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC)
        );
    }

    private function createField(ReflectionProperty $prop): FieldMetadata
    {
        $attributes = $prop->getAttributes();
        $propName = $prop->getName();
        $propValue = $prop->isInitialized($this) === true ? $prop->getValue($this) : $prop->getDefaultValue();
        if (count($attributes) === 0) {
            return $this->createFieldFromAnnotation($propName, $propValue);
        }
        return $this->createFieldFromNativeAttribute($propName, $propValue, $attributes);
    }

    private function createFieldFromAnnotation(string $propName, mixed $propValue): FieldMetadata
    {
        $annotation = new PropertyAnnotation($this, $propName, $this->attributeConfigMap);
        $attributes = $annotation->parse();
        $className = $attributes->getFieldClassName();
        return (new FieldMetadataFactory(new $className(), new FieldMetadataConfig($attributes)))
            ->create($propName, $attributes->getValueAttributeOrElse($propValue));
    }

    private function createFieldFromNativeAttribute(string $propName, mixed $propValue, array $attributes): FieldMetadata
    {
        $attributes = new AttributeCollection(
            array_map(
                fn (ReflectionAttribute $attr) => $attr->newInstance(),
                $attributes
            )
        );
        $attributes->addDefaultsBasedOnMissingAttributes($propName);
        $className = $attributes->getFieldClassName();
        return (new FieldMetadataFactory(new $className(), new FieldMetadataConfig($attributes)))
            ->create($propName, $attributes->getValueAttributeOrElse($propValue));
    }

    private function setValues(array $givenValues): void
    {
        $matchedValues = array_intersect_key(
            // MUST BE FIRST
            $givenValues,
            get_object_vars($this),
        );

        foreach ($matchedValues as $property => $value) {
            $this->$property = $value;
        }
    }

    /**
     * Returns the entry's column names.
     *
     * @return string[]
     */
    public function getColumnNames(): array
    {
        return array_column($this->fields, "column");
    }

    /**
     * Returns the model's property names.
     *
     * This can be used when the serialized column names
     * are different in format from the model's property names.
     *
     * @return string[]
     */
    public function getPropertyNames(): array
    {
        return array_column($this->fields, "propertyName");
    }

    /**
     * Returns the values of the model's fields
     */
    public function getFieldValues(): array
    {
        return array_map(fn (FieldMetadata $field) => $field->getValue(), $this->fields);
    }

    /**
     * Returns the field metadata
     *
     * @return FieldMetadata[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
