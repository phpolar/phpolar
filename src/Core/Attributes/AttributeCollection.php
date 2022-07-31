<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Attributes;

use Efortmeyer\Polar\Core\Fields\AutomaticDateField;
use Efortmeyer\Polar\Stock\Attributes\DefaultFormControl;
use Efortmeyer\Polar\Stock\Attributes\Input;

class AttributeCollection
{
    /**
     * @var Attribute[]
     */
    private $internalArray = [];


    /**
     * @param Attribute[] $attributes
     */
    public function __construct(array $attributes)
    {
        $this->internalArray = $attributes;
    }

    public function containsClass(string $className): bool
    {
        return (bool) count(
            array_filter(
                $this->internalArray,
                fn (Attribute $attribute) => is_a($attribute, $className)
            )
        );
    }

    public function getValueAttributeOrNull(): ?Attribute
    {
        foreach ($this->internalArray as $attribute) {
            if ($attribute->isAutomaticDateInput() === true) {
                return $attribute;
            }
        }
        // attribute not found
        return null;
    }

    /**
     * @throws RequiredAttributeNotFoundException
     */
    public function getLabelAttribute(): Attribute
    {
        foreach ($this->internalArray as $attribute) {
            if ($attribute->isLabel() === true) {
                return $attribute;
            }
        }

        throw new RequiredAttributeNotFoundException("Label attribute not found");
    }

    /**
     * @throws RequiredAttributeNotFoundException
     */
    public function getColumnAttribute(): Attribute
    {
        foreach ($this->internalArray as $attribute) {
            if ($attribute->isColumn() === true) {
                return $attribute;
            }
        }
        throw new RequiredAttributeNotFoundException("Column attribute not found");
    }

    /**
     * @throws RequiredAttributeNotFoundException
     */
    public function getDateFormatAttributeOrNull(): ?Attribute
    {
        foreach ($this->internalArray as $attribute) {
            if ($attribute->isDateFormat() === true) {
                return $attribute;
            }
        }
        // attribute not found
        return null;
    }

    public function getFieldClassName(): string
    {
        $automaticDateFormControls = array_filter($this->internalArray, fn (Attribute $attribute) => $attribute->isAutomaticDateInput() === true);
        $otherFormControls = array_filter($this->internalArray, fn (Attribute $attribute) => $attribute->isFormControl() === true && $attribute->isAutomaticDateInput() === false);

        if (count($automaticDateFormControls) > 0) {
            return AutomaticDateField::class;
        } else if (count($otherFormControls) > 0) {
            $formControl = current($otherFormControls);
            return ($formControl !== false ? $formControl : new DefaultFormControl())->getFieldClassName();
        } else {
            return "";
        }
    }

    /**
     * @throws RequiredAttributeNotFoundException
     */
    public function getFormControlAttribute(): Attribute
    {
        foreach ($this->internalArray as $attribute) {
            if ($attribute->isFormControl() === true) {
                return $attribute->isAutomaticDateInput() === true ? new Input(InputTypes::DATE) : $attribute;
            }
        }
        throw new RequiredAttributeNotFoundException("Form control type not found");
    }

    /**
     * @return Attribute[]
     */
    public function getValidatorAttributes(): array
    {
        return array_filter($this->internalArray, fn (Attribute $attribute) => $attribute->isValidator() === true);
    }
}
