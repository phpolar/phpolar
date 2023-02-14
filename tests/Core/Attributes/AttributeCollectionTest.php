<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Core\Attributes;

use Phpolar\Phpolar\Core\Fields\NumberField;
use Phpolar\Phpolar\Stock\Attributes\Column;
use Phpolar\Phpolar\Stock\Attributes\DateFormat;
use Phpolar\Phpolar\Stock\Attributes\DefaultColumn;
use Phpolar\Phpolar\Stock\Attributes\DefaultDateFormat;
use Phpolar\Phpolar\Stock\Attributes\DefaultFormControl;
use Phpolar\Phpolar\Stock\Attributes\DefaultLabel;
use Phpolar\Phpolar\Stock\Attributes\Input;
use Phpolar\Phpolar\Stock\Attributes\Label;
use Phpolar\Phpolar\Tests\Fakes\RequiredAttributes;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Core\Attributes\AttributeCollection
 *
 * @uses \Phpolar\Phpolar\Core\Attributes\Attribute
 * @uses \Phpolar\Phpolar\Stock\Attributes\Input
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultColumn
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultLabel
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultMaxLength
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultDateFormat
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
