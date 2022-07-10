<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock;

use DateTimeImmutable;
use Efortmeyer\Polar\Api\Attributes\AttributeInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\Field
 *
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultColumn
 * @uses \Efortmeyer\Polar\Stock\Attributes\Column
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultDateFormat
 * @uses \Efortmeyer\Polar\Stock\Attributes\DateFormat
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultFormControl
 * @uses \Efortmeyer\Polar\Stock\Attributes\Input
 * @uses \Efortmeyer\Polar\Stock\Attributes\InputTypes
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultLabel
 * @uses \Efortmeyer\Polar\Stock\Attributes\Label
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\NoopValidate
 * @uses \Efortmeyer\Polar\Stock\Attributes\TypeValidation
 * @uses \Efortmeyer\Polar\Stock\Attributes\MaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\AutomaticDateValue
 * @uses \Efortmeyer\Polar\Stock\Field
 * @uses \Efortmeyer\Polar\Stock\TextField
 * @uses \Efortmeyer\Polar\Stock\Validation\MaxLength
 * @uses \Efortmeyer\Polar\Stock\Validation\Noop
 * @uses \Efortmeyer\Polar\Stock\Validation\TypeValidation
 */
class FieldTest extends TestCase
{
    /**
     * @test
     * @dataProvider Efortmeyer\Polar\Tests\DataProviders\FieldTestData::values
     */
    public function shouldReturnTheValue($givenValue, string $format, $expected)
    {
        $sut = Field::create("testProperty", $givenValue, []);
        $sut->format = $format;
        $this->assertEquals($expected, $sut->getValue());
    }
    /**
     * @test
     * @dataProvider \Efortmeyer\Polar\Tests\DataProviders\FieldTestData::setPropertyTestCases
     */
    public function shouldSetExpectedPropertyWithExpectedValueWhenGivenSpecificAttribute(
        $givenValue,
        string $expectedSetProperty,
        $expectedValue,
        AttributeInterface $givenAttribute
    ) {
        $createdField = Field::create("testProperty", $givenValue, [$givenAttribute]);
        if ($expectedValue instanceof DateTimeImmutable) {
            $differenceAsString = $expectedValue->diff($createdField->$expectedSetProperty)->format("%d days %h hours %s seconds");
            $this->assertSame("0 days 0 hours 0 seconds", $differenceAsString);
        } else {
            $this->assertEquals($expectedValue, $createdField->$expectedSetProperty);
        }
    }

    /**
     * @test
     * @dataProvider \Efortmeyer\Polar\Tests\DataProviders\FieldTestData::validatorTestCases
     */
    public function shouldSetValidatorsWithExpectedValidatorWhenGivenSpecificAttribute(
        $givenValue,
        string $expectedValidatorClassName,
        AttributeInterface $givenAttribute
    ) {
        $createdField = Field::create("testProperty", $givenValue, [$givenAttribute]);
        $this->assertContainsOnlyInstancesOf($expectedValidatorClassName, $createdField->validators);
    }

    /**
     * @test
     */
    public function shouldNotSetAnyPropertiesWhenNoAttributeIsGiven()
    {
        $createdField = Field::create("testProperty", "", []);

        $this->assertEmpty(
            array_filter(
                array_diff_key(
                    get_object_vars($createdField),
                    ["formControlType" => "", "propertyName" => ""]
                )
            )
        );
    }

    /**
     * @test
     * @dataProvider \Efortmeyer\Polar\Tests\DataProviders\FieldTestData::fieldTypeTestCases
     */
    public function shouldCreateExpectedFieldTypeBasedOnFormControlAttributeConfiguration(
        string $expectedFieldClassName,
        $givenValue,
        AttributeInterface $givenAttribute
    ) {
        $actualField = Field::create("testProperty", $givenValue, [$givenAttribute]);
        $this->assertInstanceOf($expectedFieldClassName, $actualField);
    }
}
