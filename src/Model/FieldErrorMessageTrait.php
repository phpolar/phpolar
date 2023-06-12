<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Phpolar\Validator\MessageGetterInterface;
use ReflectionAttribute;
use ReflectionObject;
use ReflectionProperty;
use Stringable;

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
     * Validation is usually only desired
     * when the user attempts to
     * create or edit a model.
     */
    protected bool $shouldValidate = false;

    /**
     * Provides an interface for
     * retrieving a fields error message.
     */
    public function getFieldErrorMessage(string $fieldName, string $stringToAppend = ""): string
    {
        $this->setErrorMsgsOnce();
        return $this->hasError($fieldName) === true ? ($this->errorMessages[$fieldName] . $stringToAppend) : "";
    }

    /**
     * Determines if a property is not valid.
     */
    public function hasError(string $fieldName): bool
    {
        if ($this->shouldValidate === false) {
            return false;
        }
        $this->setErrorMsgsOnce();
        return isset($this->errorMessages[$fieldName]);
    }

    /**
     * Changes the posted state of the model.
     */
    public function isPosted(): void
    {
        $this->shouldValidate = true;
    }

    /**
     * Selects one of the provided validation
     * strings based on the state of the model
     *
     * @param string $invalidAttr The HTML attribute that denotes invalid state
     * @param string $validAttr The HTML attribute that denotes valid state
     *
     * @return string The selected HTML attribute that corresponds with the state of the model
     */
    public function selectValAttr(string $propName, string $invalidAttr, string $validAttr): string
    {
        return $this->shouldValidate === false ? "" : ($this->hasError($propName) === true ? $invalidAttr : $validAttr);
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
            function (ReflectionProperty $prop): void {
                $errorMessages = self::getErrorsFromAttributes(
                    $this->getMessageGetters($prop),
                );
                array_walk(
                    $errorMessages,
                    function (string | Stringable $err) use ($prop): void {
                        $this->errorMessages[$prop->getName()] = (string) $err;
                    }
                );
            }
        );
    }

    /**
     * Provides a way of retrieving only the validator attributes of a property.
     *
     * Returns only validation attributes.
     *
     * @return MessageGetterInterface[]
     */
    private function getMessageGetters(ReflectionProperty $prop): array
    {
        return array_map(
            function (ReflectionAttribute $attr) use ($prop): MessageGetterInterface {
                $instance = $attr->newInstance();
                if (method_exists($instance, "withRequiredPropVal") === true) {
                    return $instance->withRequiredPropVal($prop, $this);
                }
                if (property_exists($instance, "propVal") === true) {
                    $instance->propVal = $prop->isInitialized($this) === true ? $prop->getValue($this) : $prop->getDefaultValue();
                }
                return $instance;
            },
            $prop->getAttributes(MessageGetterInterface::class, ReflectionAttribute::IS_INSTANCEOF),
        );
    }

    /**
     * Provides a way of retrieving errors from the invalid validation attributes of a property.
     *
     * Returns errors from invalid validators.
     *
     * @param MessageGetterInterface[] $attrs
     * @return (string|Stringable)[]
     */
    private static function getErrorsFromAttributes(array $attrs): array
    {
        return array_merge(
            ...array_map(
                static fn (MessageGetterInterface $attr) => $attr->getMessages(),
                $attrs
            )
        );
    }
}
