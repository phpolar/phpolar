<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock;

use Efortmeyer\Polar\Api\Attributes\Config\Collection as AttributeConfigCollection;
use Efortmeyer\Polar\Stock\Field;
use Efortmeyer\Polar\Stock\PropertyAnnotation;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;

/**
 * Represents an item in a collection or a row in a table.
 */
abstract class Entry
{
    /**
     * @var Field[]
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
            array_map(function ($prop) { return $prop->getName(); }, $properties),
            array_map(function ($prop) { return $prop->getValue($this); }, $properties)
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

    private function createFieldFromAnnotation(string $propertyName, $propertyValue): Field
    {
        $annotation = new PropertyAnnotation($this, $propertyName, $this->attributeConfigMap);
        $attributes = $annotation->parse();
        return Field::create($propertyName, $propertyValue, $attributes);
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
        return array_map(function (Field $field) {
            return $field->getValue();
        }, $this->fields);
    }

    /**
     * Returns the field metadata
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
