<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use ReflectionNamedType;
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
                $propType = $publicProp->getType();
                return match (true) {
                    $propType instanceof ReflectionNamedType => match ($propType->getName()) {
                        "string" => $this->getStringVal($publicProp),
                        "int" => $this->getIntVal($publicProp),
                        default => null,
                    },
                    default => match (true) {
                        $publicProp->isInitialized($this) => match (gettype($publicProp->getValue($this))) {
                            "string" => $this->getStringVal($publicProp),
                            "integer" => $this->getIntVal($publicProp),
                            default => null,
                        },
                        default => null,
                    }
                };
            }
        }
        return null;
    }

    private function getStringVal(ReflectionProperty $prop): string
    {
        return $prop->isInitialized($this) === true ? strval($prop->getValue($this)) : "";
    }

    private function getIntVal(ReflectionProperty $prop): int
    {
        return $prop->isInitialized($this) === true ? intval($prop->getValue($this)) : 0;
    }
}
