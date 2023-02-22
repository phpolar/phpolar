<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Phpolar\Phpolar\Validation\ValidatorInterface;
use ReflectionObject;
use ReflectionProperty;

use function Phpolar\Phpolar\Validation\Functions\getValidationAttributes;

/**
 * Use to add support for validating the properties of an object.
 */
trait ValidationTrait
{
    /**
     * Determines if the configured properties of an object
     * are valid.
     *
     * The configuration is used to determine validity.
     *
     * @api
     */
    public function isValid(): bool
    {
        return array_reduce(
            (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC),
            $this->validateProperty(...),
            true
        );
    }

    /**
     * Uses validation attributes to determine if the
     * property is valid.
     */
    private function validateProperty(bool $prev, ReflectionProperty $prop): bool
    {
        return $prev && array_reduce(
            getValidationAttributes($prop, $this),
            static fn (bool $previousResult, ValidatorInterface $currentAttribute) =>
                $previousResult && $currentAttribute->isValid(),
            true
        );
    }
}
