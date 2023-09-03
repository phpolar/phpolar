<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Attributes;

use Efortmeyer\Polar\Core\Fields\NumberField;
use Efortmeyer\Polar\Stock\Attributes\Column;
use Efortmeyer\Polar\Stock\Attributes\DateFormat;
use Efortmeyer\Polar\Stock\Attributes\DefaultColumn;
use Efortmeyer\Polar\Stock\Attributes\DefaultDateFormat;
use Efortmeyer\Polar\Stock\Attributes\DefaultFormControl;
use Efortmeyer\Polar\Stock\Attributes\DefaultLabel;
use Efortmeyer\Polar\Stock\Attributes\Input;
use Efortmeyer\Polar\Stock\Attributes\Label;
use Efortmeyer\Polar\Tests\Fakes\RequiredAttributes;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Core\Attributes\AttributeCollection
 *
 * @uses \Efortmeyer\Polar\Core\Attributes\Attribute
 * @uses \Efortmeyer\Polar\Stock\Attributes\Input
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultColumn
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultLabel
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultDateFormat
 */
class AttributeCollectionTest extends TestCase
{
    public function requiredAttributes()
    {
        return [
            [RequiredAttributes::get()]
        ];
    }

    /**
     * @test
     * @dataProvider requiredAttributes
     */
    public function shouldReturnLabelAttribute(array $attributes)
    {
        $sut = new AttributeCollection($attributes);
        $attribute = $sut->getLabelAttribute();
        $this->assertTrue(
            $attribute instanceof Label || $attribute instanceof DefaultLabel
        );
    }

    /**
     * @test
     */
    public function shouldThrowIfCollectionDoesNotContainLabel()
    {
        $this->expectException(RequiredAttributeNotFoundException::class);
        $sut = new AttributeCollection([]);
        $sut->getLabelAttribute();
    }

    /**
     * @test
     * @dataProvider requiredAttributes
     */
    public function shouldReturnColumnAttribute(array $attributes)
    {
        $sut = new AttributeCollection($attributes);
        $attribute = $sut->getColumnAttribute();
        $this->assertTrue(
            $attribute instanceof Column || $attribute instanceof DefaultColumn
        );
    }

    /**
     * @test
     */
    public function shouldThrowIfCollectionDoesNotContainColumn()
    {
        $this->expectException(RequiredAttributeNotFoundException::class);
        $sut = new AttributeCollection([]);
        $sut->getColumnAttribute();
    }

    /**
     * @test
     * @dataProvider requiredAttributes
     */
    public function shouldReturnDateFormatAttribute(array $attributes)
    {
        $sut = new AttributeCollection($attributes);
        $attribute = $sut->getDateFormatAttributeOrNull();
        $this->assertTrue(
            $attribute instanceof DateFormat || $attribute instanceof DefaultDateFormat
        );
    }

    /**
     * @test
     */
    public function shouldReturnNullIfCollectionDoesNotContainDateFormat()
    {
        $sut = new AttributeCollection([]);
        $result = $sut->getDateFormatAttributeOrNull();
        $this->assertNull($result);
    }

    /**
     * @test
     * @dataProvider requiredAttributes
     */
    public function shouldReturnFormControlTypeAttribute(array $attributes)
    {
        $sut = new AttributeCollection($attributes);
        $attribute = $sut->getFormControlAttribute();
        $this->assertTrue(
            $attribute instanceof Input || $attribute instanceof DefaultFormControl
        );
    }

    /**
     * @test
     */
    public function shouldThrowIfCollectionDoesNotContainFormControlType()
    {
        $this->expectException(RequiredAttributeNotFoundException::class);
        $sut = new AttributeCollection([]);
        $sut->getFormControlAttribute();
    }

    /**
     * @test
     * @dataProvider requiredAttributes
     */
    public function shouldKnowIfCollectionContainsClass(array $attributes)
    {
        $sut = new AttributeCollection($attributes);
        $this->assertTrue($sut->containsClass(DefaultLabel::class));
    }

    /**
     * @test
     */
    public function shouldKnowIfCollectionContainsInput()
    {
        $sut = new AttributeCollection([...RequiredAttributes::getWithoutFormControl(), new Input(InputTypes::Number)]);
        $formControl = $sut->getFormControlAttribute();
        $this->assertSame($formControl->getFieldClassName(), NumberField::class);
    }
}