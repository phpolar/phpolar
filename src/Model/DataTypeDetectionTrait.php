<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Phpolar\StorageDriver\DataTypeUnknown;
use Phpolar\StorageDriver\StorageDriverInterface;
use Phpolar\StorageDriver\TypeName;
use ReflectionProperty;
use ReflectionNamedType;
use Stringable;

/**
 * Provides support for configuring column parameters used to create tables.
 */
trait DataTypeDetectionTrait
{
    /**
     * Uses the property type to determine the data type type.
     *
     * @api
     */
    public function getDataType(
        string $propName,
        StorageDriverInterface $storageDriver
    ): Stringable|DataTypeUnknown {
        $property = new ReflectionProperty($this, $propName);
        $propertyType = $property->getType();
        return match ($propertyType instanceof ReflectionNamedType) {
            true =>
                $this->getDataTypeFromDeclaredProperty($propertyType, $storageDriver),
            default =>
                $this->getDataTypeFromNotDeclaredProperty($property, $storageDriver),
        };
    }

    private function getDataTypeFromDeclaredProperty(
        ReflectionNamedType $propertyType,
        StorageDriverInterface $storageDriver,
    ): Stringable|DataTypeUnknown {
        $propertyTypeName = $propertyType->getName();
        $typeName = parseTypeName($propertyTypeName);
        return match ($typeName) {
            TypeName::Invalid => new DataTypeUnknown(),
            default => $storageDriver->getDataType($typeName),
        };
    }

    private function getDataTypeFromNotDeclaredProperty(
        ReflectionProperty $property,
        StorageDriverInterface $storageDriver,
    ): Stringable|DataTypeUnknown {
        return match ($property->isInitialized($this)) {
            true => match (is_object($value = $property->getValue($this))) {
                true => match (get_class($value)) {
                    "DateTimeImmutable", "DateTime" =>
                        $storageDriver->getDataType(TypeName::T_DateTime),
                    default => new DataTypeUnknown(),
                },
                default => $storageDriver->getDataType(parseTypeName(gettype($value))),
            },
            default => new DataTypeUnknown(),
        };
    }
}
