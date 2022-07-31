<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\UIElements;

use Efortmeyer\Polar\Core\Attributes\AttributeCollection;
use Efortmeyer\Polar\Core\Attributes\InputTypes;
use Efortmeyer\Polar\Core\Fields\FieldMetadata;
use Efortmeyer\Polar\Core\Fields\FieldMetadataConfig;
use Efortmeyer\Polar\Stock\Attributes\AutomaticDateValue;
use Efortmeyer\Polar\Stock\Attributes\DefaultColumn;
use Efortmeyer\Polar\Stock\Attributes\DefaultDateFormat;
use Efortmeyer\Polar\Stock\Attributes\DefaultLabel;
use Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength;
use Efortmeyer\Polar\Stock\Attributes\Input;
use Efortmeyer\Polar\Stock\Validation\MaxLength;
use Efortmeyer\Polar\Tests\Fakes\RequiredAttributes;
use Efortmeyer\Polar\Tests\Mocks\UnknownFieldType;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \Efortmeyer\Polar\Api\UIElements\FormControl
 *
 * @uses \Efortmeyer\Polar\Api\UIElements\TextFormControl
 * @uses \Efortmeyer\Polar\Api\UIElements\TextAreaFormControl
 * @uses \Efortmeyer\Polar\Api\Validation\ValidationInterface
 * @uses \Efortmeyer\Polar\Core\Attributes\Attribute
 * @uses \Efortmeyer\Polar\Core\Attributes\AttributeCollection
 * @uses \Efortmeyer\Polar\Core\Fields\FieldMetadata
 * @uses \Efortmeyer\Polar\Core\Fields\FieldMetadataConfig
 * @uses \Efortmeyer\Polar\Core\Fields\FieldMetadataFactory
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultColumn
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultLabel
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultDateFormat
 * @uses \Efortmeyer\Polar\Stock\Attributes\MaxLength
 * @uses \Efortmeyer\Polar\Stock\Validation\MaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\AutomaticDateValue
 * @uses \Efortmeyer\Polar\Stock\Attributes\Input
 * @testdox FormControl
 */
class FormControlTest extends TestCase
{
    /**
     * @var Attribute[]
     */
    public $requiredAttributes;

    public function setUp(): void
    {
        $this->requiredAttributes = [
            new DefaultLabel(""),
            new DefaultColumn(""),
            new DefaultDateFormat(),
            new DefaultMaxLength(""),
            new Input(InputTypes::TEXT),
        ];
    }

    public static function fieldWithoutErrorsTestCases()
    {
        $requiredAttributes = [
            new DefaultLabel(""),
            new DefaultColumn(""),
            new DefaultDateFormat(),
            new DefaultMaxLength(""),
        ];
        return [
            [FieldMetadata::getFactory(new AttributeCollection([...$requiredAttributes, new Input(InputTypes::TEXT)]))->create("testProperty", "")],
        ];
    }

    public function fieldErrorsTestCases()
    {
        /**
         * @var Stub $attributeStub
         */
        $attributeStub = $this->createStub(MaxLength::class);
        $attributeStub->method("isValid")
            ->willReturn(false);
        $attributeStub->method("getErrorMessage")
            ->willReturn(Messages::ERROR_MESSAGE);
        $field = FieldMetadata::getFactory(new AttributeCollection(RequiredAttributes::get()))->create("testProperty", "");
        $field->validators[] = $attributeStub;

        return [
            [$field],
        ];
    }

    public function fieldExpectationTestCases()
    {
        $requiredAttributes = [
            new DefaultLabel(""),
            new DefaultColumn(""),
            new DefaultDateFormat(),
            new DefaultMaxLength(""),
        ];
        return [
            [
                TextFormControl::class,
                FieldMetadata::getFactory(new AttributeCollection([...$requiredAttributes, new Input(InputTypes::TEXT)]))->create("testProperty", ""),
            ],
            [
                TextAreaFormControl::class,
                FieldMetadata::getFactory(new AttributeCollection([...$requiredAttributes, new Input(InputTypes::TEXTAREA)]))->create("testProperty", ""),
            ],
            [
                NumberFormControl::class,
                FieldMetadata::getFactory(new AttributeCollection([...$requiredAttributes, new Input(InputTypes::NUMBER)]))->create("testProperty", ""),
            ],
            [
                DateFormControl::class,
                FieldMetadata::getFactory(new AttributeCollection([...$requiredAttributes, new Input(InputTypes::DATE)]))->create("testProperty", null),
            ],
            [
                HiddenFormControl::class,
                FieldMetadata::getFactory(new AttributeCollection([...$requiredAttributes, new AutomaticDateValue()]))->create("testProperty", null),
            ],
        ];
    }

    /**
     * @test
     * @dataProvider fieldExpectationTestCases
     */
    public function shouldCreateExpectedFormControlBasedOnGivenFieldMetadata(string $expectedClass, FieldMetadata $givenField)
    {
        $this->assertInstanceOf($expectedClass, FormControl::create($givenField));
    }

    /**
     * @test
     */
    public function shouldThrowRuntimeExceptionWhenGivenFieldThatWithUnknownType()
    {
        $this->expectException(RuntimeException::class);
        FormControl::create(UnknownFieldType::create("propertyName", "", FieldMetadataConfig::create(new AttributeCollection(RequiredAttributes::get()))));
    }

    /**
     * @test
     */
    public function shouldNotReturnErrorMessageWhenFieldDoesNotHaveErrors()
    {
        $sut = FormControl::create(FieldMetadata::getFactory(new AttributeCollection(RequiredAttributes::get()))->create("testProperty", ""));
        $this->assertEmpty($sut->getErrorMesage());
    }

    /**
     * @test
     * @dataProvider fieldErrorsTestCases
     */
    public function shouldReturnErrorMessageWhenFieldHasErrors(FieldMetadata $field)
    {
        $sut = FormControl::create($field);
        $this->assertNotEmpty($sut->getErrorMesage());
    }

    /**
     * @test
     * @dataProvider fieldWithoutErrorsTestCases
     */
    public function shouldNotReturnErrorStylingWhenFieldDoesNotHaveErrors(FieldMetadata $field)
    {
        $sut = FormControl::create($field);
        $this->assertEmpty($sut->getErrorStyling());
    }

    /**
     * @test
     * @dataProvider fieldErrorsTestCases
     */
    public function shouldReturnErrorStylingWhenFieldHasErrors(FieldMetadata $field)
    {
        $sut = FormControl::create($field);
        $this->assertNotEmpty($sut->getErrorStyling());
    }

    /**
     * @test
     * @dataProvider fieldWithoutErrorsTestCases
     */
    public function shouldBeValidWhenFieldDoesNotHaveErrors(FieldMetadata $field)
    {
        $sut = FormControl::create($field);
        $this->assertFalse($sut->isInvalid());
    }

    /**
     * @test
     * @dataProvider fieldErrorsTestCases
     */
    public function shouldBeInvalidWhenFieldHasErrors(FieldMetadata $field)
    {
        $sut = FormControl::create($field);
        $this->assertTrue($sut->isInvalid());
    }

    /**
     * @test
     * @dataProvider fieldWithoutErrorsTestCases
     */
    public function shouldGetTheLabelOfTheField(FieldMetadata $field)
    {
        $fakeLabel = uniqid();
        $field->label = $fakeLabel;
        $sut = FormControl::create($field);
        $this->assertSame($fakeLabel, $sut->getLabel());
    }

    /**
     * @test
     * @dataProvider fieldWithoutErrorsTestCases
     */
    public function shouldGetTheNameOfTheField(FieldMetadata $field)
    {
        $fakeName = uniqid();
        $field->propertyName = $fakeName;
        $sut = FormControl::create($field);
        $this->assertSame($fakeName, $sut->getName());
    }

    /**
     * @test
     * @dataProvider fieldWithoutErrorsTestCases
     */
    public function shouldGetTheValueOfTheField(FieldMetadata $field)
    {
        $fakeValue = uniqid();
        $field->value = $fakeValue;
        $sut = FormControl::create($field);
        $this->assertSame($fakeValue, $sut->getValue());
    }
}
