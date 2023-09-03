<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api\UIElements;

use Phpolar\Phpolar\Core\Attributes\AttributeCollection;
use Phpolar\Phpolar\Core\Attributes\InputTypes;
use Phpolar\Phpolar\Core\Fields\FieldMetadata;
use Phpolar\Phpolar\Core\Fields\FieldMetadataConfig;
use Phpolar\Phpolar\Core\Fields\FieldMetadataFactory;
use Phpolar\Phpolar\Stock\Attributes\AutomaticDateValue;
use Phpolar\Phpolar\Stock\Attributes\DefaultColumn;
use Phpolar\Phpolar\Stock\Attributes\DefaultDateFormat;
use Phpolar\Phpolar\Stock\Attributes\DefaultLabel;
use Phpolar\Phpolar\Stock\Attributes\DefaultMaxLength;
use Phpolar\Phpolar\Stock\Attributes\Input;
use Phpolar\Phpolar\Stock\Validation\MaxLength;
use Phpolar\Phpolar\Tests\Fakes\RequiredAttributes;
use Phpolar\Phpolar\Tests\Mocks\UnknownFieldType;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * @covers \Phpolar\Phpolar\Api\UIElements\FormControl
 *
 * @uses \Phpolar\Phpolar\Api\UIElements\TextFormControl
 * @uses \Phpolar\Phpolar\Api\UIElements\TextAreaFormControl
 * @uses \Phpolar\Phpolar\Api\Validation\ValidationInterface
 * @uses \Phpolar\Phpolar\Core\Attributes\Attribute
 * @uses \Phpolar\Phpolar\Core\Attributes\AttributeCollection
 * @uses \Phpolar\Phpolar\Core\Fields\FieldMetadata
 * @uses \Phpolar\Phpolar\Core\Fields\FieldMetadataConfig
 * @uses \Phpolar\Phpolar\Core\Fields\FieldMetadataFactory
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultColumn
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultMaxLength
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultLabel
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultDateFormat
 * @uses \Phpolar\Phpolar\Stock\Attributes\MaxLength
 * @uses \Phpolar\Phpolar\Stock\Validation\MaxLength
 * @uses \Phpolar\Phpolar\Stock\Attributes\AutomaticDateValue
 * @uses \Phpolar\Phpolar\Stock\Attributes\Input
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
            new Input(InputTypes::Text),
        ];
    }

    private static function getFactory(AttributeCollection $attrs): FieldMetadataFactory
    {
        $className = $attrs->getFieldClassName();
        return new FieldMetadataFactory(
            new $className(),
            new FieldMetadataConfig($attrs),
        );
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
            [self::getFactory(new AttributeCollection([...$requiredAttributes, new Input(InputTypes::Text)]))->create("testProperty", "")],
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
        $field = self::getFactory(new AttributeCollection(RequiredAttributes::get()))->create("testProperty", "");
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
                self::getFactory(new AttributeCollection([...$requiredAttributes, new Input(InputTypes::Text)]))->create("testProperty", ""),
            ],
            [
                TextAreaFormControl::class,
                self::getFactory(new AttributeCollection([...$requiredAttributes, new Input(InputTypes::Textarea)]))->create("testProperty", ""),
            ],
            [
                NumberFormControl::class,
                self::getFactory(new AttributeCollection([...$requiredAttributes, new Input(InputTypes::Number)]))->create("testProperty", ""),
            ],
            [
                DateFormControl::class,
                self::getFactory(new AttributeCollection([...$requiredAttributes, new Input(InputTypes::Date)]))->create("testProperty", null),
            ],
            [
                HiddenFormControl::class,
                self::getFactory(new AttributeCollection([...$requiredAttributes, new AutomaticDateValue()]))->create("testProperty", null),
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
        FormControl::create(UnknownFieldType::create("propertyName", "", new FieldMetadataConfig(new AttributeCollection(RequiredAttributes::get()))));
    }

    /**
     * @test
     */
    public function shouldNotReturnErrorMessageWhenFieldDoesNotHaveErrors()
    {
        $sut = FormControl::create(self::getFactory(new AttributeCollection(RequiredAttributes::get()))->create("testProperty", ""));
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
