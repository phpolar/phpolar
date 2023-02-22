<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use ReflectionClass;
use ReflectionObject;

/**
 * Allows for configuring the name of entity.
 */
trait EntityNameConfigurationTrait
{
    /**
     * Returns the name of the entity
     *
     * @api
     */
    public function getName(): string
    {
        $attrs = (new ReflectionObject($this))->getAttributes(EntityName::class);
        if (count($attrs) < 1) {
            return (new ReflectionClass(static::class))->getShortName();
        }
        $attr = $attrs[0];
        return $attr->newInstance()->getName();
    }
}
