<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\DataProviders;

use Efortmeyer\Polar\Core\Attributes\InputTypes;
use Efortmeyer\Polar\Core\Fields\{
    AutomaticDateField,
    DateField,
    FieldMetadata,
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
use Efortmeyer\Polar\Stock\Attributes\Label;
use Efortmeyer\Polar\Stock\Attributes\MaxLength;
use Efortmeyer\Polar\Stock\Attributes\NoopValidate;
use Efortmeyer\Polar\Stock\Attributes\TypeValidation;
use Efortmeyer\Polar\Stock\Validation\MaxLength as ValidationMaxLength;
use Efortmeyer\Polar\Stock\Validation\Noop;
use Efortmeyer\Polar\Stock\Validation\ScalarTypes;
use Efortmeyer\Polar\Stock\Validation\TypeValidation as ValidationTypeValidation;
use Efortmeyer\Polar\Stock\Attributes\AutomaticDateValue;
use Efortmeyer\Polar\Stock\Attributes\Defaults;
use Efortmeyer\Polar\Stock\Attributes\Input;

use DateTimeImmutable;
use Efortmeyer\Polar\Core\Attributes\Attribute;
use Efortmeyer\Polar\Core\Attributes\AttributeCollection;
use Efortmeyer\Polar\Tests\Fakes\RequiredAttributes;

class FieldMetadataTestData
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
        $getLabelTestCase = fn (string $propName, Attribute $attribute) => [
            $propName,
            $attribute(),
            FieldMetadata::getFactory(new AttributeCollection([...RequiredAttributes::getWithoutLabel(), $attribute]))->create("testProperty", "")
        ];
        $getColumnTestCase = fn (string $propName, Attribute $attribute) => [
            $propName,
            $attribute(),
            FieldMetadata::getFactory(new AttributeCollection([...RequiredAttributes::getWithoutColumn(), $attribute]))->create("testProperty", "")
        ];
        $getDateFormatTestCase = fn (string $propName, Attribute $attribute) => [
            $propName,
            $attribute(),
            FieldMetadata::getFactory(new AttributeCollection([...RequiredAttributes::getWithoutDateFormat(), $attribute]))->create("testProperty", "")
        ];
        $getFormControlTestCase = fn (string $propName, Attribute $attribute) => [
            $propName,
            $attribute(),
            FieldMetadata::getFactory(new AttributeCollection([...RequiredAttributes::getWithoutFormControl(), $attribute]))->create("testProperty", $text)
        ];
        $getAutomaticDateTestCase = fn ($propName, Attribute $attribute) => [
            $propName,
            $attribute(),
            FieldMetadata::getFactory(new AttributeCollection([...RequiredAttributes::getWithoutFormControl(), $attribute]))->create("testProperty", null)
        ];

        return [
            $getLabelTestCase("label", new Label($text)),
            $getLabelTestCase("label", new DefaultLabel($text)),
            $getColumnTestCase("column", new Column($text)),
            $getColumnTestCase("column", new DefaultColumn($text)),
            $getDateFormatTestCase("dateFormat", new DateFormat(Defaults::DATE_FORMAT)),
            $getDateFormatTestCase("dateFormat", new DefaultDateFormat($dateTime)),
            $getFormControlTestCase("formControlType", new Input(InputTypes::Text)),
            $getFormControlTestCase("formControlType", new DefaultFormControl()),
            $getAutomaticDateTestCase("value", new AutomaticDateValue()),
        ];
    }

    public static function validatorTestCases()
    {
        $text = uniqid();
        $maxLengthVal = str_repeat("a", random_int(1, 10));
        $maxLength = strlen($maxLengthVal);

        $maxLengthAttribute = new MaxLength($maxLengthVal, $maxLength);
        $typeValidationAttribute = new TypeValidation($text, ScalarTypes::String->value);
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
            [TextField::class, "", new Input(InputTypes::Text)],
            [TextAreaField::class, "", new Input(InputTypes::Textarea)],
            [NumberField::class, "", new Input(InputTypes::Number)],
            [DateField::class, "", new Input(InputTypes::Date)],
            [AutomaticDateField::class, null, new AutomaticDateValue()],
        ];
    }
}
