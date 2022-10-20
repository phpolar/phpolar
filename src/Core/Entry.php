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
    public function __construct(private AttributeConfigCollection $attributeConfigMap, array $storedValues = [])
    {
        if (empty($storedValues) === false) {
            $this->setValues($storedValues);
        }

        /**
         * The annotations/attributes on the entry's properties
         * are used to configure the fields.
         */
        $this->fields = array_merge(
            $this->createFieldsFromNativeAttributes(),
            $this->createFieldsFromAnnotations(),
        );
    }

    /**
     * @return FieldMetadata[]
     */
    private function createFieldsFromAnnotations(): array
    {
        $properties = array_filter(
            (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC),
            fn (ReflectionProperty $prop) => count($prop->getAttributes()) === 0
        );

        return array_map(
            function (string $propertyName, mixed $propertyValue): FieldMetadata {
                $annotation = new PropertyAnnotation($this, $propertyName, $this->attributeConfigMap);
                $attributes = $annotation->parse();
                $className = $attributes->getFieldClassName();
                return (new FieldMetadataFactory(new $className(), new FieldMetadataConfig($attributes)))
                    ->create($propertyName, $attributes->getValueAttributeOrElse($propertyValue));
            },
            array_map(fn (ReflectionProperty $prop) => $prop->getName(), $properties),
            array_map(fn (ReflectionProperty $prop) => $prop->isInitialized($this) === true ? $prop->getValue($this) : $prop->getDefaultValue(), $properties)
        );
    }

    /**
     * @return FieldMetadata[]
     */
    private function createFieldsFromNativeAttributes(): array
    {
        return array_map(
            function (ReflectionProperty $property): FieldMetadata {
                $propertyName = $property->getName();
                $propertyValue = $property->isInitialized($this) === true ? $property->getValue($this) : $property->getDefaultValue();
                $attributes = new AttributeCollection(
                    array_map(
                        fn (ReflectionAttribute $attr) => $attr->newInstance(),
                        $property->getAttributes()
                    )
                );
                $attributes->addDefaultsBasedOnMissingAttributes($propertyName);
                $className = $attributes->getFieldClassName();
                return (new FieldMetadataFactory(new $className(), new FieldMetadataConfig($attributes)))
                    ->create($propertyName, $attributes->getValueAttributeOrElse($propertyValue));
            },
            array_filter(
                (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC),
                fn (ReflectionProperty $prop) => count($prop->getAttributes()) > 0
            )
        );
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
