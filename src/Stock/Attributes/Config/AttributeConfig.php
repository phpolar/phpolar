<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes\Config;

use Efortmeyer\Polar\Api\Attributes\Config\AttributeConfigInterface;
use Efortmeyer\Polar\Core\Attributes\Config\ConstructorArgs;

class AttributeConfig implements AttributeConfigInterface
{
    /**
     * @var ConstructorArgs
     */
    protected $constructorArgType;

    /**
     * @var ConstructorArgs
     */
    protected $constructorArgTypeForDefault;

    /**
     * @var ?string
     */
    protected $configuredType;

    /**
     * @var string The class name of the Attribute to use when the property has no configuration.
     */
    protected $defaultClassName;


    public function __construct(
        ConstructorArgs $constructorArgType,
        string $defaultClassName,
        ConstructorArgs $constructorArgTypeForDefault,
        ?string $forType = null
    ) {
        $this->constructorArgType = $constructorArgType;
        $this->defaultClassName = $defaultClassName;
        $this->constructorArgTypeForDefault = $constructorArgTypeForDefault;
        $this->configuredType = $forType;
    }

    public function getConstructorArgType(): ConstructorArgs
    {
        return $this->constructorArgType;
    }

    public function getClassNameForDefaultAttribute(): string
    {
        return $this->defaultClassName;
    }

    public function getConstructorArgTypeForDefault(): ConstructorArgs
    {
        return $this->constructorArgTypeForDefault;
    }

    public function isConfiguredForClass(): bool
    {
        return $this->configuredType !== null;
    }

    public function forType(): string
    {
        return $this->configuredType ?? "";
    }
}
