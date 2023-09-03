<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Phpolar\Phpolar\Core\Validation\Exception\ValidatorWithNoErrorMessageException;
use Phpolar\Phpolar\Validation\AbstractValidationError;
use Phpolar\Phpolar\Validation\DefaultValidationError;
use Phpolar\Phpolar\Validation\ValidatorInterface;
use ReflectionObject;
use ReflectionProperty;

use function Phpolar\Phpolar\Validation\Functions\getValidationAttributes;

/**
 * Provides support for displaying form field error messages.
 */
trait FieldErrorMessageTrait
{
    /**
     * Stores error messages.
     *
     * @var array<string,string>
     */
    private array $errorMessages;

    private bool $checked = false;

    /**
     * Provides an interface for
     * retrieving a fields error message.
     *
     * @api
     *
     * @throws ValidatorWithNoErrorMessageException
     */
    public function getFieldErrorMessage(string $fieldName, string $stringToAppend = ""): string
    {
        if ($this->checked === false) {
            $this->checked = true;
            $this->setErrorMessages();
        }
        $hasError = isset($this->errorMessages[$fieldName]);
        return $hasError === true ? ($this->errorMessages[$fieldName] . $stringToAppend) : "";
    }

    private function setErrorMessages(): void
    {
        $props = (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC);
        array_walk(
            $props,
            function (ReflectionProperty $prop) {
                $errorAttrs = self::getErrorsFromAttributes(
                    getValidationAttributes($prop, $this),
                );
                array_walk(
                    $errorAttrs,
                    function (AbstractValidationError $err) use ($prop) {
                        $this->errorMessages[$prop->getName()] = $err->getMessage();
                    }
                );
            }
        );
    }

    /**
     * Provides a way of retrieving errors from the invalid validation attributes of a property.
     *
     * Returns errors from invalid validators.
     *
     * @param ValidatorInterface[] $attrs
     * @return AbstractValidationError[]
     */
    private static function getErrorsFromAttributes(array $attrs): array
    {
        return array_map(
            static fn (ValidatorInterface $attr) => new DefaultValidationError($attr),
            array_filter(
                $attrs,
                static fn (ValidatorInterface $attr) => $attr->isValid() === false,
            ),
        );
    }
}
