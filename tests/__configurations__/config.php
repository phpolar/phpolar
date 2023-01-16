<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Phpolar\Phpolar\Api\Attributes\Config\Collection;
use Phpolar\Phpolar\Core\Attributes\Config\{
    ConstructorArgsNone,
    ConstructorArgsOne,
    ConstructorArgsPropertyName,
    ConstructorArgsPropertyValue,
    ConstructorArgsPropValWithSndArg,
};
use Phpolar\Phpolar\Core\Attributes\Config\AttributeConfig;
use Phpolar\Phpolar\Stock\Attributes\Config\ColumnKey;
use Phpolar\Phpolar\Stock\Attributes\Config\DateFormatKey;
use Phpolar\Phpolar\Stock\Attributes\Config\LabelKey;
use Phpolar\Phpolar\Stock\Attributes\Config\MaxLengthKey;
use Phpolar\Phpolar\Stock\Attributes\Config\TypeValidationKey;

use DateTimeInterface;
use Phpolar\Phpolar\Stock\Attributes\Config\AutomaticDateValueKey;
use Phpolar\Phpolar\Stock\Attributes\Config\InputKey;

$configCollection = new Collection();


$configCollection->add(
    new ColumnKey(),
    new class(
        new ConstructorArgsPropertyName(),
        DefaultColumn::class,
        new ConstructorArgsPropertyName(),
    ) extends AttributeConfig
    {
    }
);
$configCollection->add(
    new LabelKey(),
    new class(
        new ConstructorArgsPropertyName(),
        DefaultLabel::class,
        new ConstructorArgsPropertyName(),
    ) extends AttributeConfig
    {
    },
);
$configCollection->add(
    new InputKey(),
    new class(
        new ConstructorArgsOne(),
        DefaultFormControl::class,
        new ConstructorArgsPropertyValue(),
    ) extends AttributeConfig
    {
    },
);
$configCollection->add(
    new MaxLengthKey(),
    new class(
        new ConstructorArgsPropValWithSndArg(),
        DefaultMaxLength::class,
        new ConstructorArgsPropertyValue(),
    ) extends AttributeConfig
    {
    },
);
$configCollection->add(
    new DateFormatKey(),
    new class(
        new ConstructorArgsOne(),
        DefaultDateFormat::class,
        new ConstructorArgsNone(),
        DateTimeInterface::class,
    ) extends AttributeConfig
    {
    },
);
$configCollection->add(
    new TypeValidationKey(),
    new class(
        new ConstructorArgsPropValWithSndArg(),
        NoopValidate::class,
        new ConstructorArgsNone()
    ) extends AttributeConfig
    {
    },
);
$configCollection->add(
    new AutomaticDateValueKey(),
    new class(
        new ConstructorArgsNone(),
        NoopValidate::class,
        new ConstructorArgsNone()
    ) extends AttributeConfig
    {
    },
);

return $configCollection;
