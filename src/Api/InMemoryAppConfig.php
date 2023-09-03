<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api;

use Efortmeyer\Polar\Api\Attributes\Config\Collection;
use Efortmeyer\Polar\Core\Attributes\Config\{
    ConstructorArgsNone,
    ConstructorArgsOne,
    ConstructorArgsPropertyName,
    ConstructorArgsPropertyValue,
    ConstructorArgsPropertyValueWithSecondArg,
};
use Efortmeyer\Polar\Stock\Attributes\Config\{
    AttributeConfig,
    AutomaticDateValueKey,
    ColumnKey,
    DateFormatKey,
    InputKey,
    LabelKey,
    MaxLengthKey,
    TypeValidationKey,
};
use Efortmeyer\Polar\Stock\Attributes\{
    DefaultColumn,
    DefaultDateFormat,
    DefaultFormControl,
    DefaultLabel,
    DefaultMaxLength,
    NoopValidate,
};

use DateTimeInterface;
use Efortmeyer\Polar\Api\Attributes\Config\Key;

/**
 * Provides an in memory interface for configuration the application.
 */
final class InMemoryAppConfig implements AppConfigInterface
{
    /**
     * @var Collection
     */
    private $configCollection;

    public function __construct()
    {
        $this->configCollection = new Collection();
    }

    /**
     * Add or override an attribute configuration.
     */
    public function add(Key $key, AttributeConfig $config): void
    {
        $this->configCollection->add($key, $config);
    }

    /**
     * Return the all registered attribute configurations.
     */
    public function getAll(): Collection
    {
        $this->configCollection->add(
            new ColumnKey(),
            new class(
                new ConstructorArgsPropertyName(),
                DefaultColumn::class,
                new ConstructorArgsPropertyName(),
            ) extends AttributeConfig
            {
            }
        );
        $this->configCollection->add(
            new LabelKey(),
            new class(
                new ConstructorArgsPropertyName(),
                DefaultLabel::class,
                new ConstructorArgsPropertyName(),
            ) extends AttributeConfig
            {
            },
        );
        $this->configCollection->add(
            new InputKey(),
            new class(
                new ConstructorArgsOne(),
                DefaultFormControl::class,
                new ConstructorArgsPropertyValue(),
            ) extends AttributeConfig
            {
            },
        );
        $this->configCollection->add(
            new MaxLengthKey(),
            new class(
                new ConstructorArgsPropertyValueWithSecondArg(),
                DefaultMaxLength::class,
                new ConstructorArgsPropertyValue(),
            ) extends AttributeConfig
            {
            },
        );
        $this->configCollection->add(
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
        $this->configCollection->add(
            new TypeValidationKey(),
            new class(
                new ConstructorArgsPropertyValueWithSecondArg(),
                NoopValidate::class,
                new ConstructorArgsNone()
            ) extends AttributeConfig
            {
            },
        );
        $this->configCollection->add(
            new AutomaticDateValueKey(),
            new class(
                new ConstructorArgsNone(),
                NoopValidate::class,
                new ConstructorArgsNone()
            ) extends AttributeConfig
            {
            },
        );
        return $this->configCollection;
    }
}