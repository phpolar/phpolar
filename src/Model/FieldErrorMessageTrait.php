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

    /**
     * Make sure the properties are
     * checked only once.
     *
     * Do not initialize this.
     * Otherwise, this property
     * will appear when iterating
     * the objects that use this
     * trait.
     */
    protected bool $checked;

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
        $this->setErrorMsgsOnce();
        return $this->hasError($fieldName) === true ? ($this->errorMessages[$fieldName] . $stringToAppend) : "";
    }

    /**
     * Determines if a property is
     * not valid.
     *
     * @api
     */
    public function hasError(string $fieldName): bool
    {
        $this->setErrorMsgsOnce();
        return isset($this->errorMessages[$fieldName]);
    }

    private function setErrorMsgsOnce(): void
    {
        if (
            (new ReflectionProperty($this, "checked"))->isInitialized($this) === true &&
                $this->checked === true
        ) {
            return;
        }
        $this->checked = true;
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
