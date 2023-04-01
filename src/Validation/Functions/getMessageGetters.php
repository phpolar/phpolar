<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation\Functions;

use Phpolar\Validator\MessageGetterInterface;
use ReflectionAttribute;
use ReflectionProperty;

/**
 * Provides a way of retrieving only the validator attributes of a property.
 *
 * Returns only validation attributes.
 *
 * @return MessageGetterInterface[]
 */
function getMessageGetters(ReflectionProperty $prop, object $obj): array
{
    return array_map(
        static function (ReflectionAttribute $attr) use ($prop, $obj): MessageGetterInterface {
            $instance = $attr->newInstance();
            if (method_exists($instance, "withRequiredPropVal") === true) {
                return $instance->withRequiredPropVal($prop, $obj);
            }
            if (property_exists($instance, "propVal") === true) {
                $instance->propVal = $prop->isInitialized($obj) === true ? $prop->getValue($obj) : $prop->getDefaultValue();
            }
            return $instance;
        },
        $prop->getAttributes(MessageGetterInterface::class, ReflectionAttribute::IS_INSTANCEOF),
    );
}
