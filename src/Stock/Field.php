<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock;

use Efortmeyer\Polar\Api\Attributes\AttributeInterface;
use Efortmeyer\Polar\Api\Validation\ValidationInterface;
use Efortmeyer\Polar\Stock\Attributes\{
    AutomaticDateValue,
    Column,
    DateFormat,
    DefaultColumn,
    DefaultDateFormat,
    DefaultFormControl,
    DefaultLabel,
    DefaultMaxLength,
    Input,
    InputTypes,
    Label,
    MaxLength,
    NoopValidate,
    TypeValidation,
};

use DateTimeInterface;

/**
 * Provides metadata for a field.
 */
class Field
{
    public string $label = "";

    public string $formControlType = "";

    public string $column = "";

    public string $propertyName = "";

    public string $format = "";

    /**
     * @var ValidationInterface[]
     */
    public array $validators = [];

    /**
     * @var mixed
     */
    public $value;

    /**
     * @param mixed $value
     * @param AttributeInterface[] $attributes
     */
    protected function __construct(string $propertyName, $value, array $attributes)
    {
        $this->propertyName = $propertyName;
        $this->value = $value;

        foreach ($attributes as $attribute) {
            switch (true) {
                case $attribute instanceof Label:
                case $attribute instanceof DefaultLabel:
                    $this->label = $attribute();
                    break;
                case $attribute instanceof Column:
                case $attribute instanceof DefaultColumn:
                    $this->column = $attribute();
                    break;
                case $attribute instanceof Input:
                case $attribute instanceof DefaultFormControl:
                    $this->formControlType = $attribute();
                    break;
                case $attribute instanceof MaxLength:
                case $attribute instanceof DefaultMaxLength:
                    $this->validators[] = $attribute();
                    break;
                case $attribute instanceof TypeValidation:
                    $this->validators[] = $attribute();
                    break;
                case $attribute instanceof NoopValidate:
                    $this->validators[] = $attribute();
                    break;
                // the automatic date field attribute has greater precedence than date field
                case $attribute instanceof AutomaticDateValue:
                    $this->value = $attribute();
                    break;
                case $attribute instanceof DateFormat:
                case $attribute instanceof DefaultDateFormat:
                    $this->format = $attribute();
                    break;
            }
        }
    }

    public function getValue()
    {
        return $this->value instanceof DateTimeInterface ? $this->value->format($this->format) : $this->value;
    }

    /**
     * Create a Field.
     *
     * @param string $propertyName
     * @param mixed $value
     * @param AttributeInterface[] $attributes
     */
    public static function create(string $propertyName, $value, array $attributes): Field
    {
        switch (true) {
            case self::isTextField($attributes):
                return new TextField($propertyName, $value, $attributes);
            case self::isTextAreaField($attributes):
                return new TextAreaField($propertyName, $value, $attributes);
            case self::isNumberField($attributes):
                return new NumberField($propertyName, $value, $attributes);
            // the automatic date field attribute has greater precedence than date field
            case self::isAutomaticDateField($attributes):
                return new AutomaticDateField($propertyName, $value, $attributes);
            case self::isDateField($attributes):
                return new DateField($propertyName, $value, $attributes);
            default:
                return new TextField($propertyName, $value, $attributes);
        }
    }

    /**
     * @param AttributeInterface[] $attributes
     */
    private static function isTextField($attributes): bool
    {
        return (bool) count(
            array_filter(
                $attributes,
                function ($attribute) {
                    return $attribute instanceof Input && InputTypes::TEXT === $attribute();
                }
            )
        );
    }

    /**
     * @param AttributeInterface[] $attributes
     */
    private static function isTextAreaField($attributes): bool
    {
        return (bool) count(
            array_filter(
                $attributes,
                function ($attribute) {
                    return $attribute instanceof Input && InputTypes::TEXTAREA === $attribute();
                }
            )
        );
    }

    /**
     * @param AttributeInterface[] $attributes
     */
    private static function isNumberField($attributes): bool
    {
        return (bool) count(
            array_filter(
                $attributes,
                function ($attribute) {
                    return $attribute instanceof Input && InputTypes::NUMBER === $attribute();
                }
            )
        );
    }

    /**
     * @param AttributeInterface[] $attributes
     */
    private static function isDateField($attributes): bool
    {
        return (bool) count(
            array_filter(
                $attributes,
                function ($attribute) {
                    return $attribute instanceof Input && InputTypes::DATE === $attribute();
                }
            )
        );
    }

    /**
     * @param AttributeInterface[] $attributes
     */
    private static function isAutomaticDateField($attributes): bool
    {
        return (bool) count(
            array_filter(
                $attributes,
                function ($attribute) {
                    return $attribute instanceof AutomaticDateValue;
                }
            )
        );
    }
}
