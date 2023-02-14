<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\AppConfig;

use Phpolar\Phpolar\Api\AppConfigInterface;
use Phpolar\Phpolar\Api\Attributes\Config\{
    Collection,
    Key,
};
use Phpolar\Phpolar\Core\Attributes\Config\{
    AttributeConfig,
    ConstructorArgsNone,
    ConstructorArgsOne,
    ConstructorArgsPropertyName,
    ConstructorArgsPropertyValue,
    ConstructorArgsPropValWithSndArg,
};
use Phpolar\Phpolar\Stock\Attributes\Config\{
    AutomaticDateValueKey,
    ColumnKey,
    DateFormatKey,
    InputKey,
    LabelKey,
    MaxLengthKey,
    TypeValidationKey,
};
use Phpolar\Phpolar\Stock\Attributes\{
    DefaultColumn,
    DefaultDateFormat,
    DefaultFormControl,
    DefaultLabel,
    DefaultMaxLength,
    NoopValidate,
};

use DateTimeInterface;

/**
 * Provides an in memory interface for configuration the application.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
final class InMemoryAppConfig implements AppConfigInterface
{
    private readonly Collection $configCollection;

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
                new ConstructorArgsPropValWithSndArg(),
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
                new ConstructorArgsPropValWithSndArg(),
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
