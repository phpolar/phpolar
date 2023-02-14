<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Phpolar\Phpolar\Api\Attributes\Config\Collection;
use Phpolar\Phpolar\Core\Attributes\Config\ConstructorArgsPropertyName;
use Phpolar\Phpolar\Core\Attributes\Config\AttributeConfig;

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
