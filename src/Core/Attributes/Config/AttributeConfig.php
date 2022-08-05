<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Attributes\Config;

class AttributeConfig implements AttributeConfigInterface
{
    public function __construct(
        protected ConstructorArgs $constructorArgType,
        protected string $defaultClassName,
        protected ConstructorArgs $constructorArgTypeForDefault,
        protected ?string $configuredType = null
    ) {
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
