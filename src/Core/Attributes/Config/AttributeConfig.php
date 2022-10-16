<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Attributes\Config;

class AttributeConfig implements AttributeConfigInterface
{
    public function __construct(
        protected readonly ConstructorArgs $constructorArgType,
        protected readonly string $defaultClassName,
        protected readonly ConstructorArgs $defaultArgType,
        protected readonly ?string $configuredType = null
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
        return $this->defaultArgType;
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
