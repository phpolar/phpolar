<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Fields;

use Efortmeyer\Polar\Core\Attributes\AttributeCollection;
use Efortmeyer\Polar\Core\Attributes\Attribute;
use Efortmeyer\Polar\Tests\Fakes\RequiredAttributes;

use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

/**
 * @covers \Efortmeyer\Polar\Core\Fields\FieldMetadata
 *
 * @uses \Efortmeyer\Polar\Core\Attributes\Attribute
 * @uses \Efortmeyer\Polar\Core\Attributes\AttributeCollection
 * @uses \Efortmeyer\Polar\Core\Fields\FieldMetadataConfig
 * @uses \Efortmeyer\Polar\Core\Fields\FieldMetadataFactory
 * @uses \Efortmeyer\Polar\Core\Fields\TextField
 * @uses \Efortmeyer\Polar\Stock\Validation\MaxLength
 * @uses \Efortmeyer\Polar\Stock\Validation\Noop
 * @uses \Efortmeyer\Polar\Stock\Validation\TypeValidation
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultColumn
 * @uses \Efortmeyer\Polar\Stock\Attributes\Column
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultDateFormat
 * @uses \Efortmeyer\Polar\Stock\Attributes\DateFormat
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultFormControl
 * @uses \Efortmeyer\Polar\Stock\Attributes\Input
 * @uses \Efortmeyer\Polar\Core\Attributes\InputTypes
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultLabel
 * @uses \Efortmeyer\Polar\Stock\Attributes\Label
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\NoopValidate
 * @uses \Efortmeyer\Polar\Stock\Attributes\TypeValidation
 * @uses \Efortmeyer\Polar\Stock\Attributes\MaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\AutomaticDateValue
 */
class FieldMetadataTest extends TestCase
{
    /**
     * @test
     * @dataProvider Efortmeyer\Polar\Tests\DataProviders\FieldMetadataTestData::values
     */
    public function shouldReturnTheValue($givenValue, string $format, $expected)
    {
        $sut = FieldMetadata::getFactory(new AttributeCollection(RequiredAttributes::get()))->create("testProperty", $givenValue);
        $sut->format = $format;
        $this->assertEquals($expected, $sut->getValue());
    }
    /**
     * @test
     * @dataProvider \Efortmeyer\Polar\Tests\DataProviders\FieldMetadataTestData::setPropertyTestCases
     */
    public function shouldSetExpectedPropertyWithExpectedValueWhenGivenSpecificAttribute(
        string $expectedSetProperty,
        $expectedValue,
        FieldMetadata $createdField
    ) {
        if ($expectedValue instanceof DateTimeImmutable) {
            $differenceAsString = $expectedValue->diff($createdField->$expectedSetProperty)->format("%d days %h hours %s seconds");
            $this->assertSame("0 days 0 hours 0 seconds", $differenceAsString);
        } else {
            $this->assertEquals($expectedValue, $createdField->$expectedSetProperty);
        }
    }

    /**
     * @test
     * @dataProvider \Efortmeyer\Polar\Tests\DataProviders\FieldMetadataTestData::validatorTestCases
     */
    public function shouldSetValidatorsWithExpectedValidatorWhenGivenSpecificAttribute(
        $givenValue,
        string $expectedValidatorClassName,
        Attribute $givenAttribute
    ) {
        $createdField = FieldMetadata::getFactory(new AttributeCollection([...RequiredAttributes::getWithoutMaxLength(), $givenAttribute]))->create("testProperty", $givenValue);
        $this->assertContainsOnlyInstancesOf($expectedValidatorClassName, $createdField->validators);
    }

    /**
     * @test
     * @dataProvider \Efortmeyer\Polar\Tests\DataProviders\FieldMetadataTestData::fieldTypeTestCases
     */
    public function shouldCreateExpectedFieldTypeBasedOnFormControlAttributeConfiguration(
        string $expectedFieldClassName,
        $givenValue,
        Attribute $givenAttribute
    ) {
        $actualField = FieldMetadata::getFactory(new AttributeCollection([...RequiredAttributes::getWithoutFormControl(), $givenAttribute]))->create("testProperty", $givenValue);
        $this->assertInstanceOf($expectedFieldClassName, $actualField);
    }
}
