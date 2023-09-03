<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use DateTimeInterface;
use Phpolar\Phpolar\Core\InputTypes;
use Phpolar\Phpolar\Core\PropertyTypeNotDeclared;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

/**
 * Adds support for detecting the form input type based on the type declaration of the property.
 *
 * If the property type is not declared, the type of the value of the property will be used.
 */
trait FormInputTypeDetectionTrait
{
    /**
     * Uses the type declaration of the property to determine the input type of the field.
     *
     * @api
     */
    public function getInputType(string $propName): InputTypes
    {
        $property = new ReflectionProperty($this, $propName);
        if (count($property->getAttributes(Hidden::class)) > 0) {
            return InputTypes::Hidden;
        }
        $propertyType = $property->getType() ?? new PropertyTypeNotDeclared();
        if ($propertyType instanceof ReflectionNamedType) {
            return match ($propertyType->getName()) {
                "string" => InputTypes::Text,
                "int", "float" => InputTypes::Number,
                "bool" => InputTypes::Checkbox,
                "DateTimeInterface", "DateTimeImmutable", "DateTime" =>
                InputTypes::Date,
                default => InputTypes::Invalid,
            };
        }
        if ($propertyType instanceof ReflectionUnionType) {
            return in_array("string", $propertyType->getTypes()) === true &&
                   in_array("array", $propertyType->getTypes()) === false ? InputTypes::Text : InputTypes::Invalid;
        }
        if ($propertyType instanceof PropertyTypeNotDeclared) {
            return $property->isInitialized($this) ? match (gettype($property->getValue($this))) {
                    "string" => InputTypes::Text,
                    "integer", "double" => InputTypes::Number,
                    "boolean" => InputTypes::Checkbox,
                    "object" => $property->getValue($this) instanceof DateTimeInterface ? InputTypes::Date : InputTypes::Invalid,
                    default => InputTypes::Invalid,
                } : InputTypes::Invalid;
        }
        return InputTypes::Invalid;
    }
}
