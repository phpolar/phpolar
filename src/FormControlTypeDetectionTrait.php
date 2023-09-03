<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use Phpolar\Phpolar\Core\PropertyTypeNotDeclared;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

/**
 * Adds support for detecting the form control type based on the type declaration of the property.
 *
 * If the property type is not declared, the type of the value of the property will be used.
 */
trait FormControlTypeDetectionTrait
{
    /**
     * Uses the type declaration of the property to determine the form control type of the field.
     *
     * @api
     */
    public function getFormControlType(string $propName): FormControlTypes
    {
        $property = new ReflectionProperty($this, $propName);
        $propertyType = $property->getType() ?? new PropertyTypeNotDeclared();
        if ($propertyType instanceof ReflectionNamedType) {
            return match ($property->getName()) {
                "string",
                "int",
                "float",
                "bool",
                DateTimeInterface::class,
                DateTimeImmutable::class,
                DateTime::class =>
                FormControlTypes::Input,
                "array" => FormControlTypes::Select,
                default => FormControlTypes::Invalid,
            };
        }
        if ($propertyType instanceof ReflectionUnionType) {
            return in_array("string", $propertyType->getTypes()) === true &&
            in_array("array", $propertyType->getTypes()) === false ? FormControlTypes::Input : FormControlTypes::Invalid;
        }
        if ($propertyType instanceof PropertyTypeNotDeclared) {
            return $property->isInitialized($this) === true ? match (gettype($property->getValue($this))) {
                "string",
                "integer",
                "double",
                "boolean" => FormControlTypes::Input,
                "object" => match (get_class($property->getValue($this))) {
                    "DateTimeInterface", "DateTimeImmutable", "DateTime" => FormControlTypes::Input,
                    default => FormControlTypes::Invalid,
                },
                "array" => FormControlTypes::Select,
                default => FormControlTypes::Invalid,
            } : FormControlTypes::Invalid;
        }
        return FormControlTypes::Invalid;
    }
}
