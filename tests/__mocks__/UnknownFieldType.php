<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\Mocks;

use Efortmeyer\Polar\Stock\Field;

final class UnknownFieldType extends Field
{
    public static function create(string $propertyName, $value, array $attributeConfig): Field
    {
        return new self($propertyName, $value, $attributeConfig);
    }
}
