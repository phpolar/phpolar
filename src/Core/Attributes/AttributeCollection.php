<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Attributes;

use Efortmeyer\Polar\Core\Fields\AutomaticDateField;
use Efortmeyer\Polar\Stock\Attributes\Column;
use Efortmeyer\Polar\Stock\Attributes\DateFormat;
use Efortmeyer\Polar\Stock\Attributes\DefaultColumn;
use Efortmeyer\Polar\Stock\Attributes\DefaultDateFormat;
use Efortmeyer\Polar\Stock\Attributes\DefaultFormControl;
use Efortmeyer\Polar\Stock\Attributes\DefaultLabel;
use Efortmeyer\Polar\Stock\Attributes\Input;
use Efortmeyer\Polar\Stock\Attributes\Label;

class AttributeCollection
{
    /**
     * @param Attribute[] $internalArray
     */
    public function __construct(private array $internalArray)
    {
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

    public function addDefaultsBasedOnMissingAttributes(string $propertyName): void
    {
        if ($this->containsClass(Label::class) === false) {
            $this->internalArray[] = new DefaultLabel($propertyName);
        }

        if ($this->containsClass(Column::class) === false) {
            $this->internalArray[] = new DefaultColumn($propertyName);
        }

        if ($this->containsClass(DateFormat::class) === false) {
            $this->internalArray[] = new DefaultDateFormat();
        }
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

    public function getValueAttributeOrElse(mixed $value): mixed
    {
        return $this->shouldGetValueAttribute() === true ? $this->getValueAttributeOrNull() : $value;
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

    public function shouldGetValueAttribute(): bool
    {
        return count(array_filter($this->internalArray, fn (Attribute $attribute) => $attribute->isAutomaticDateInput() === true)) > 0;
    }
}
