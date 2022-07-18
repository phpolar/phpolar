<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\DataProviders;

use Efortmeyer\Polar\Core\Defaults;
use Efortmeyer\Polar\Stock\{
    AutomaticDateField,
    DateField,
    NumberField,
    TextField,
    TextAreaField,
};
use Efortmeyer\Polar\Stock\Attributes\Column;
use Efortmeyer\Polar\Stock\Attributes\DateFormat;
use Efortmeyer\Polar\Stock\Attributes\DefaultColumn;
use Efortmeyer\Polar\Stock\Attributes\DefaultDateFormat;
use Efortmeyer\Polar\Stock\Attributes\DefaultFormControl;
use Efortmeyer\Polar\Stock\Attributes\DefaultLabel;
use Efortmeyer\Polar\Stock\Attributes\InputTypes;
use Efortmeyer\Polar\Stock\Attributes\Label;
use Efortmeyer\Polar\Stock\Attributes\MaxLength;
use Efortmeyer\Polar\Stock\Attributes\NoopValidate;
use Efortmeyer\Polar\Stock\Attributes\TypeValidation;
use Efortmeyer\Polar\Stock\Validation\MaxLength as ValidationMaxLength;
use Efortmeyer\Polar\Stock\Validation\Noop;
use Efortmeyer\Polar\Stock\Validation\ScalarTypes;
use Efortmeyer\Polar\Stock\Validation\TypeValidation as ValidationTypeValidation;

use DateTimeImmutable;
use Efortmeyer\Polar\Stock\Attributes\AutomaticDateValue;
use Efortmeyer\Polar\Stock\Attributes\Input;

class FieldTestData
{
    private const BOOLEAN_VALUES = [true, false];

    public static function values()
    {
        $str = uniqid();
        $number = random_int(1, 1000000);
        $boolean = self::BOOLEAN_VALUES[array_rand(self::BOOLEAN_VALUES)];
        $dateTime = new DateTimeImmutable();
        return [
            [$str, "", $str],
            [$boolean, "", $boolean],
            [$number, "", $number],
            [$dateTime, Defaults::DATE_FORMAT, $dateTime->format(Defaults::DATE_FORMAT)],
        ];
    }
    public function setPropertyTestCases()
    {
        $text = uniqid();
        $dateTime = new DateTimeImmutable();
        $getTestCase = fn ($propName, $attribute) => [$text, $propName, $attribute(), $attribute];

        return array_merge(
            array_map($getTestCase, ["label", "label"], [new Label($text), new DefaultLabel($text)]),
            array_map($getTestCase, ["column", "column"], [new Column($text), new DefaultColumn($text)]),
            array_map($getTestCase, ["formControlType", "formControlType"], [new Input(Defaults::FORM_CONTROL_TYPE), new DefaultFormControl()]),
            array_map($getTestCase, ["format", "format"], [new DateFormat(Defaults::DATE_FORMAT), new DefaultDateFormat($dateTime)]),
            array_map($getTestCase, ["value"], [new AutomaticDateValue()]),

        );
    }

    public static function validatorTestCases()
    {
        $text = uniqid();
        $maxLengthVal = str_repeat("a", random_int(1, 10));
        $maxLength = strlen($maxLengthVal);

        $maxLengthAttribute = new MaxLength($maxLengthVal, $maxLength);
        $typeValidationAttribute = new TypeValidation($text, ScalarTypes::STRING);
        $noopAttribute = new NoopValidate();

        return [
            [$maxLengthVal, ValidationMaxLength::class, $maxLengthAttribute],
            [$text, ValidationTypeValidation::class, $typeValidationAttribute],
            [$text, Noop::class, $noopAttribute],
        ];
    }

    public static function fieldTypeTestCases()
    {
        return [
            [TextField::class, "", new Input(InputTypes::TEXT)],
            [TextAreaField::class, "", new Input(InputTypes::TEXTAREA)],
            [NumberField::class, "", new Input(InputTypes::NUMBER)],
            [DateField::class, "", new Input(InputTypes::DATE)],
            [AutomaticDateField::class, "", new AutomaticDateValue(InputTypes::DATE)],
        ];
    }
}
