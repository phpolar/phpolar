<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\Attributes\Config;

use Efortmeyer\Polar\Core\Attributes\Config\ConstructorArgs;

/**
 * Provides a way to retrieve
 * metadata for an attribute.
 */
interface AttributeConfigInterface
{
    /**
     * Provides the contructor argument type.
     *
     * @api
     */
    public function getConstructorArgType(): ConstructorArgs;

    /**
     * Provides the class name for the default attribute.
     *
     * @api
     */
    public function getClassNameForDefaultAttribute(): string;

    /**
     * Provides the contructor argument type for the default attribute.
     *
     * @api
     */
    public function getConstructorArgTypeForDefault(): ConstructorArgs;

    /**
     * Use to determine if the attribute is for a specified type.
     *
     * @api
     */
    public function forType(): string;

    /**
     * Use to deterimine if the attribute is configured for any class.
     *
     * @api
     */
    public function isConfiguredForClass(): bool;
}
