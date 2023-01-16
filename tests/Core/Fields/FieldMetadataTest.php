<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Core\Fields;

use Phpolar\Phpolar\Core\Attributes\AttributeCollection;
use Phpolar\Phpolar\Core\Attributes\Attribute;
use Phpolar\Phpolar\Tests\Fakes\RequiredAttributes;

use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

/**
 * @covers \Phpolar\Phpolar\Core\Fields\FieldMetadata
 *
 * @uses \Phpolar\Phpolar\Core\Attributes\Attribute
 * @uses \Phpolar\Phpolar\Core\Attributes\AttributeCollection
 * @uses \Phpolar\Phpolar\Core\Fields\FieldMetadataConfig
 * @uses \Phpolar\Phpolar\Core\Fields\FieldMetadataFactory
 * @uses \Phpolar\Phpolar\Core\Fields\TextField
 * @uses \Phpolar\Phpolar\Stock\Validation\MaxLength
 * @uses \Phpolar\Phpolar\Stock\Validation\Noop
 * @uses \Phpolar\Phpolar\Stock\Validation\TypeValidation
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultColumn
 * @uses \Phpolar\Phpolar\Stock\Attributes\Column
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultDateFormat
 * @uses \Phpolar\Phpolar\Stock\Attributes\DateFormat
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultFormControl
 * @uses \Phpolar\Phpolar\Stock\Attributes\Input
 * @uses \Phpolar\Phpolar\Core\Attributes\InputTypes
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultLabel
 * @uses \Phpolar\Phpolar\Stock\Attributes\Label
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultMaxLength
 * @uses \Phpolar\Phpolar\Stock\Attributes\NoopValidate
 * @uses \Phpolar\Phpolar\Stock\Attributes\TypeValidation
 * @uses \Phpolar\Phpolar\Stock\Attributes\MaxLength
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultMaxLength
 * @uses \Phpolar\Phpolar\Stock\Attributes\AutomaticDateValue
 */
class FieldMetadataTest extends TestCase
{
    private static function getFactory(AttributeCollection $attrs): FieldMetadataFactory
    {
        $className = $attrs->getFieldClassName();
        return new FieldMetadataFactory(
            new $className(),
            new FieldMetadataConfig($attrs),
        );
    }

    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\FieldMetadataTestData::values
     */
    public function shouldReturnTheValue($givenValue, string $format, $expected)
    {
        $sut = self::getFactory(new AttributeCollection(RequiredAttributes::get()))->create("testProperty", $givenValue);
        $sut->format = $format;
        $this->assertEquals($expected, $sut->getValue());
    }
    /**
     * @test
     * @dataProvider \Phpolar\Phpolar\Tests\DataProviders\FieldMetadataTestData::setPropertyTestCases
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
     * @dataProvider \Phpolar\Phpolar\Tests\DataProviders\FieldMetadataTestData::validatorTestCases
     */
    public function shouldSetValidatorsWithExpectedValidatorWhenGivenSpecificAttribute(
        $givenValue,
        string $expectedValidatorClassName,
        Attribute $givenAttribute
    ) {
        $createdField = self::getFactory(new AttributeCollection([...RequiredAttributes::getWithoutMaxLength(), $givenAttribute]))->create("testProperty", $givenValue);
        $this->assertContainsOnlyInstancesOf($expectedValidatorClassName, $createdField->validators);
    }

    /**
     * @test
     * @dataProvider \Phpolar\Phpolar\Tests\DataProviders\FieldMetadataTestData::fieldTypeTestCases
     */
    public function shouldCreateExpectedFieldTypeBasedOnFormControlAttributeConfiguration(
        string $expectedFieldClassName,
        $givenValue,
        Attribute $givenAttribute
    ) {
        $actualField = self::getFactory(new AttributeCollection([...RequiredAttributes::getWithoutFormControl(), $givenAttribute]))->create("testProperty", $givenValue);
        $this->assertInstanceOf($expectedFieldClassName, $actualField);
    }
}
