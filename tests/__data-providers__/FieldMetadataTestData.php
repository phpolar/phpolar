<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

use Phpolar\Phpolar\Core\Attributes\InputTypes;
use Phpolar\Phpolar\Core\Fields\{
    AutomaticDateField,
    DateField,
    FieldMetadataConfig,
    FieldMetadataFactory,
    NumberField,
    TextField,
    TextAreaField,
};
use Phpolar\Phpolar\Stock\Attributes\Column;
use Phpolar\Phpolar\Stock\Attributes\DateFormat;
use Phpolar\Phpolar\Stock\Attributes\DefaultColumn;
use Phpolar\Phpolar\Stock\Attributes\DefaultDateFormat;
use Phpolar\Phpolar\Stock\Attributes\DefaultFormControl;
use Phpolar\Phpolar\Stock\Attributes\DefaultLabel;
use Phpolar\Phpolar\Stock\Attributes\Label;
use Phpolar\Phpolar\Stock\Attributes\MaxLength;
use Phpolar\Phpolar\Stock\Attributes\NoopValidate;
use Phpolar\Phpolar\Stock\Attributes\TypeValidation;
use Phpolar\Phpolar\Stock\Validation\MaxLength as ValidationMaxLength;
use Phpolar\Phpolar\Stock\Validation\Noop;
use Phpolar\Phpolar\Stock\Validation\ScalarTypes;
use Phpolar\Phpolar\Stock\Validation\TypeValidation as ValidationTypeValidation;
use Phpolar\Phpolar\Stock\Attributes\AutomaticDateValue;
use Phpolar\Phpolar\Stock\Attributes\Defaults;
use Phpolar\Phpolar\Stock\Attributes\Input;

use DateTimeImmutable;
use Phpolar\Phpolar\Core\Attributes\Attribute;
use Phpolar\Phpolar\Core\Attributes\AttributeCollection;
use Phpolar\Phpolar\Tests\Fakes\RequiredAttributes;

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
        $fieldMetadataFnc = function (AttributeCollection $attrs) {
            $className = $attrs->getFieldClassName();
            return new FieldMetadataFactory(
                new $className(),
                new FieldMetadataConfig($attrs),
            );
        };
        $getLabelTestCase = fn (string $propName, Attribute $attribute) => [
            $propName,
            $attribute(),
            $fieldMetadataFnc(new AttributeCollection([...RequiredAttributes::getWithoutLabel(), $attribute]))->create("testProperty", "")
        ];
        $getColumnTestCase = fn (string $propName, Attribute $attribute) => [
            $propName,
            $attribute(),
            $fieldMetadataFnc(new AttributeCollection([...RequiredAttributes::getWithoutColumn(), $attribute]))->create("testProperty", "")
        ];
        $getDateFormatTestCase = fn (string $propName, Attribute $attribute) => [
            $propName,
            $attribute(),
            $fieldMetadataFnc(new AttributeCollection([...RequiredAttributes::getWithoutDateFormat(), $attribute]))->create("testProperty", "")
        ];
        $getFormControlTestCase = fn (string $propName, Attribute $attribute) => [
            $propName,
            $attribute(),
            $fieldMetadataFnc(new AttributeCollection([...RequiredAttributes::getWithoutFormControl(), $attribute]))->create("testProperty", $text)
        ];
        $getAutomaticDateTestCase = fn ($propName, Attribute $attribute) => [
            $propName,
            $attribute(),
            $fieldMetadataFnc(new AttributeCollection([...RequiredAttributes::getWithoutFormControl(), $attribute]))->create("testProperty", null)
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

        $maxLengthAttribute = new MaxLength($maxLength, $maxLengthVal);
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
