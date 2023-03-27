<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use ReflectionObject;
use ReflectionProperty;

/**
 * Provides support for configuring properties as primary keys.
 */
trait PrimaryKeyTrait
{
    /**
     * Retrieve the value of the primary key property.
     */
    public function getPrimaryKey(): string | int | null
    {
        foreach ((new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC) as $publicProp) {
            if (count($publicProp->getAttributes(PrimaryKey::class)) !== 0) {
                if ($publicProp->isInitialized($this) === true) {
                    return $publicProp->getValue($this);
                }
                return match ($publicProp->getType()->getName()) {
                    "string" => "",
                    "int" => 0,
                    default => null,
                };
            }
        }
        return null;
    }
}
