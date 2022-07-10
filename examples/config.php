<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Api\Attributes\Config\Collection;
use Efortmeyer\Polar\Core\Attributes\Config\ConstructorArgsPropertyName;
use Efortmeyer\Polar\Stock\Attributes\Config\AttributeConfig;

use MyCustomAttributeConfigKey;

$configCollection = new Collection();

$configCollection->add(
    new MyCustomAttributeConfigKey(),
    new class(
        new ConstructorArgsPropertyName(),
        DefaultColumn::class,
        new ConstructorArgsPropertyName(),
    ) extends AttributeConfig
    {
    }
);

return $configCollection;
