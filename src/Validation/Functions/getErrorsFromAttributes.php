<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation\Functions;

use Phpolar\Phpolar\Validation\ValidationError;
use Phpolar\Phpolar\Validation\ValidationErrorInterface;
use Phpolar\Phpolar\Validation\ValidatorInterface;

/**
 * Provides a way of retrieving errors from the invalid validation attributes of a property.
 *
 * Returns errors from invalid validators
 *
 * @param ValidatorInterface[] $attrs
 * @return ValidationErrorInterface[]
 */
function getErrorsFromAttributes(array $attrs): array
{
    return array_map(
        static fn (ValidatorInterface $attr) => new ValidationError($attr),
        array_filter(
            $attrs,
            static fn (ValidatorInterface $attr) => $attr->isValid() === false,
        ),
    );
}
