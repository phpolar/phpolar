<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Api\Attributes\Config\Collection;
use Efortmeyer\Polar\Core\Attributes\Config\{
    ConstructorArgsNone,
    ConstructorArgsOne,
    ConstructorArgsPropertyName,
    ConstructorArgsPropertyValue,
    ConstructorArgsPropertyValueWithSecondArg,
};
use Efortmeyer\Polar\Core\Attributes\Config\AttributeConfig;
use Efortmeyer\Polar\Stock\Attributes\Config\ColumnKey;
use Efortmeyer\Polar\Stock\Attributes\Config\DateFormatKey;
use Efortmeyer\Polar\Stock\Attributes\Config\LabelKey;
use Efortmeyer\Polar\Stock\Attributes\Config\MaxLengthKey;
use Efortmeyer\Polar\Stock\Attributes\Config\TypeValidationKey;

use DateTimeInterface;
use Efortmeyer\Polar\Stock\Attributes\Config\AutomaticDateValueKey;
use Efortmeyer\Polar\Stock\Attributes\Config\InputKey;

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
        new ConstructorArgsPropertyValueWithSecondArg(),
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
        new ConstructorArgsPropertyValueWithSecondArg(),
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