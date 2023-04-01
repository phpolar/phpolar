<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation\Functions;

use Phpolar\Validator\ValidatorInterface;
use ReflectionAttribute;
use ReflectionProperty;

/**
 * Provides a way of retrieving only the validator attributes of a property.
 *
 * Returns only validation attributes.
 *
 * @return ValidatorInterface[]
 */
function getValidators(ReflectionProperty $prop, object $obj): array
{
    return array_map(
        static function (ReflectionAttribute $attr) use ($prop, $obj): ValidatorInterface {
            $instance = $attr->newInstance();
            if (method_exists($instance, "withRequiredPropVal") === true) {
                return $instance->withRequiredPropVal($prop, $obj);
            }
            if (property_exists($instance, "propVal") === true) {
                $instance->propVal = $prop->isInitialized($obj) === true ? $prop->getValue($obj) : $prop->getDefaultValue();
            }
            return $instance;
        },
        $prop->getAttributes(ValidatorInterface::class, ReflectionAttribute::IS_INSTANCEOF),
    );
}
