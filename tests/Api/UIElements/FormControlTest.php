<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\UIElements;

use Efortmeyer\Polar\Core\Defaults;
use Efortmeyer\Polar\Stock\Attributes\AutomaticDateValue;
use Efortmeyer\Polar\Stock\Attributes\Input;
use Efortmeyer\Polar\Stock\Attributes\InputTypes;
use Efortmeyer\Polar\Stock\AutomaticDateField;
use Efortmeyer\Polar\Stock\DateField;
use Efortmeyer\Polar\Stock\Field;
use Efortmeyer\Polar\Stock\NumberField;
use Efortmeyer\Polar\Stock\TextAreaField;
use Efortmeyer\Polar\Stock\TextField;
use Efortmeyer\Polar\Stock\Validation\MaxLength;
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
 * @uses \Efortmeyer\Polar\Stock\Field
 * @uses \Efortmeyer\Polar\Stock\Attributes\AutomaticDateValue
 * @uses \Efortmeyer\Polar\Stock\Attributes\Input
 * @testdox FormControl
 */
class FormControlTest extends TestCase
{
    public static function fieldWithoutErrorsTestCases()
    {
        return [
            [Field::create("testProperty", "", [])],
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
            ->willReturn(Defaults::ERROR_MESSAGE);
        $field = Field::create("testProperty", "", []);
        $field->validators[] = $attributeStub;

        return [
            [$field],
        ];
    }

    /**
     * @test
     */
    public function shouldCreateTextFormControlWhenGivenTextFieldAttribute()
    {
        $sut = FormControl::create(TextField::create("testProperty", "", [new Input(InputTypes::TEXT)]));
        $this->assertInstanceOf(TextFormControl::class, $sut);
    }

    /**
     * @test
     */
    public function shouldCreateTextAreaFormControlWhenGivenTextAreaFieldAttribute()
    {
        $sut = FormControl::create(TextAreaField::create("testProperty", "", [new Input(InputTypes::TEXTAREA)]));
        $this->assertInstanceOf(TextAreaFormControl::class, $sut);
    }

    /**
     * @test
     */
    public function shouldCreateNumberFormControlWhenGivenNumberFieldAttribute()
    {
        $sut = FormControl::create(NumberField::create("testProperty", "", [new Input(InputTypes::NUMBER)]));
        $this->assertInstanceOf(NumberFormControl::class, $sut);
    }

    /**
     * @test
     */
    public function shouldCreateDateFormControlWhenGivenDateFieldAttribute()
    {
        $sut = FormControl::create(DateField::create("testProperty", null, [new Input(InputTypes::DATE)]));
        $this->assertInstanceOf(DateFormControl::class, $sut);
    }

    /**
     * @test
     */
    public function shouldCreateHiddenFormControlWhenGivenAutomaticDateFieldAttribute()
    {
        $sut = FormControl::create(AutomaticDateField::create("testProperty", null, [new AutomaticDateValue()]));
        $this->assertInstanceOf(HiddenFormControl::class, $sut);
    }

    /**
     * @test
     */
    public function shouldThrowRuntimeExceptionWhenGivenFieldThatWithUnknownType()
    {
        $this->expectException(RuntimeException::class);
        FormControl::create(UnknownFieldType::create("propertyName", "", []));
    }

    /**
     * @test
     */
    public function shouldNotReturnErrorMessageWhenFieldDoesNotHaveErrors()
    {
        $sut = FormControl::create(Field::create("testProperty", "", []));
        $this->assertEmpty($sut->getErrorMesage());
    }

    /**
     * @test
     * @dataProvider fieldErrorsTestCases
     */
    public function shouldReturnErrorMessageWhenFieldHasErrors($field)
    {
        $sut = FormControl::create($field);
        $this->assertNotEmpty($sut->getErrorMesage());
    }

    /**
     * @test
     * @dataProvider fieldWithoutErrorsTestCases
     */
    public function shouldNotReturnErrorStylingWhenFieldDoesNotHaveErrors($field)
    {
        $sut = FormControl::create($field);
        $this->assertEmpty($sut->getErrorStyling());
    }

    /**
     * @test
     * @dataProvider fieldErrorsTestCases
     */
    public function shouldReturnErrorStylingWhenFieldHasErrors($field)
    {
        $sut = FormControl::create($field);
        $this->assertNotEmpty($sut->getErrorStyling());
    }

    /**
     * @test
     * @dataProvider fieldWithoutErrorsTestCases
     */
    public function shouldBeValidWhenFieldDoesNotHaveErrors($field)
    {
        $sut = FormControl::create($field);
        $this->assertFalse($sut->isInvalid());
    }

    /**
     * @test
     * @dataProvider fieldErrorsTestCases
     */
    public function shouldBeInvalidWhenFieldHasErrors($field)
    {
        $sut = FormControl::create($field);
        $this->assertTrue($sut->isInvalid());
    }

    /**
     * @test
     */
    public function shouldGetTheLabelOfTheField()
    {
        $fakeLabel = uniqid();
        $field = Field::create("testProperty", "", []);
        $field->label = $fakeLabel;
        $sut = FormControl::create($field);
        $this->assertSame($fakeLabel, $sut->getLabel());
    }

    /**
     * @test
     */
    public function shouldGetTheNameOfTheField()
    {
        $fakeName = uniqid();
        $field = Field::create("testProperty", "", []);
        $field->propertyName = $fakeName;
        $sut = FormControl::create($field);
        $this->assertSame($fakeName, $sut->getName());
    }

    /**
     * @test
     */
    public function shouldGetTheValueOfTheField()
    {
        $fakeValue = uniqid();
        $field = Field::create("testProperty", $fakeValue, []);
        $sut = FormControl::create($field);
        $this->assertSame($fakeValue, $sut->getValue());
    }
}
