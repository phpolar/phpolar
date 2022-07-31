<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core;

use Efortmeyer\Polar\Api\Attributes\Config\Collection as AttributeConfigCollection;
use Efortmeyer\Polar\Core\Fields\FieldMetadata;
use Efortmeyer\Polar\Stock\Attributes\AutomaticDateValue;
use ReflectionClass;
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
    private array $fields;

    private AttributeConfigCollection $attributeConfigMap;

    /**
     * @param AttributeConfigCollection $attributeConfigMap
     * @param array $storedValues
     *
     * @throws RuntimeException
     */
    public function __construct(AttributeConfigCollection $attributeConfigMap, array $storedValues = [])
    {
        if (empty($storedValues) === false) {
            $this->setValues($storedValues);
        }

        $this->attributeConfigMap = $attributeConfigMap;

        $properties = (new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC);

        $this->fields = array_map(
            [$this, "createFieldFromAnnotation"],
            array_map(fn ($prop) => $prop->getName(), $properties),
            array_map(fn ($prop) => $prop->getValue($this), $properties)
        );
    }

    private function createFieldFromAnnotation(string $propertyName, $propertyValue): FieldMetadata
    {
        $annotation = new PropertyAnnotation($this, $propertyName, $this->attributeConfigMap);
        $attributes = $annotation->parse();
        $value = $attributes->containsClass(AutomaticDateValue::class) === true ? $attributes->getValueAttributeOrNull() : $propertyValue;
        $field = FieldMetadata::getFactory($attributes)->create($propertyName, $value);
        return $field;
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
     *
     * @return mixed[]
     */
    public function getFieldValues(): array
    {
        return array_map(fn (FieldMetadata $field) => $field->getValue(), $this->fields);
    }

    /**
     * Returns the field metadata
     * @return FieldMetadata[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
