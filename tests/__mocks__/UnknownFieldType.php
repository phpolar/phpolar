<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Mocks;

use Phpolar\Phpolar\Core\Fields\FieldMetadata;
use Phpolar\Phpolar\Core\Fields\FieldMetadataConfig;

final class UnknownFieldType extends FieldMetadata
{
    public static function create(string $propertyName, $value, FieldMetadataConfig $attributeConfig): FieldMetadata
    {
        return new self($propertyName, $value, $attributeConfig);
    }
}
