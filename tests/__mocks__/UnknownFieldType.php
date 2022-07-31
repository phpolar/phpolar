<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\Mocks;

use Efortmeyer\Polar\Core\Fields\FieldMetadata;
use Efortmeyer\Polar\Core\Fields\FieldMetadataConfig;

final class UnknownFieldType extends FieldMetadata
{
    public static function create(string $propertyName, $value, FieldMetadataConfig $attributeConfig): FieldMetadata
    {
        return new self($propertyName, $value, $attributeConfig);
    }
}
