<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation\Functions;

use Phpolar\Phpolar\Validation\Max;
use Phpolar\Phpolar\Validation\MaxLength;
use Phpolar\Phpolar\Validation\Min;
use Phpolar\Phpolar\Validation\MinLength;
use Phpolar\Phpolar\Validation\Pattern;
use Phpolar\Phpolar\Validation\Required;
use Phpolar\Phpolar\Validation\ValidatorInterface;
use ReflectionAttribute;
use ReflectionProperty;

/**
 * Provides a way of retrieving only the validation attributes of a property.
 *
 * Returns only validation attributes.
 *
 * @return ValidatorInterface[]
 */
function getValidationAttributes(ReflectionProperty $prop, object $obj): array
{
    return array_map(
        static fn (ReflectionAttribute $attr): Max|MaxLength|Min|MinLength|Pattern|Required =>
            $attr->newInstance()->withPropVal($prop, $obj),
        array_merge(
            $prop->getAttributes(Max::class),
            $prop->getAttributes(MaxLength::class),
            $prop->getAttributes(Min::class),
            $prop->getAttributes(MinLength::class),
            $prop->getAttributes(Pattern::class),
            $prop->getAttributes(Required::class),
        ),
    );
}
