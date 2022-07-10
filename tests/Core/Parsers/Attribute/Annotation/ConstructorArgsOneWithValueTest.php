<?php

declare(strict_types=1);


namespace Efortmeyer\Polar\Core\Parsers\Annotation;

use DateTimeImmutable;
use DateTimeInterface;
use Efortmeyer\Polar\Stock\Attributes\DateFormat;
use Efortmeyer\Polar\Stock\Attributes\DefaultDateFormat;
use Efortmeyer\Polar\Stock\Attributes\DefaultLabel;
use Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength;
use Efortmeyer\Polar\Stock\Attributes\Label;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOneWithValue
 * @covers \Efortmeyer\Polar\Core\Parsers\Annotation\Token
 * @covers \Efortmeyer\Polar\Core\Parsers\Annotation\Constructor
 *
 * @uses \Efortmeyer\Polar\Stock\Attributes\Label
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultLabel
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultDateFormat
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultMaxLength
 * @testdox ConstructorArgsOne
 */
class ConstructorArgsOneWithValueTest extends TestCase
{
    /**
     * @test
     */
    public function shouldParseStringIntoExpectedAttribute()
    {
        $givenDocComment = <<<JAVASCRIPT
        /**
         * @var string
         * @Label(test)
         * @Input(text)
         */
        JAVASCRIPT;
        $expectedClass = Label::class;
        $sut = new ConstructorArgsOneWithValue(Label::class, "Label", DefaultLabel::class, ["FAKE_STRING"], "FAKE_STRING");
        $actualInstance = $sut->toToken($givenDocComment)->newInstance();
        $this->assertInstanceOf($expectedClass, $actualInstance);
    }

    /**
     * @test
     */
    public function shouldReturnDefaultAttributeWhenAnnotationIsNotConfigured()
    {
        $givenDocComment = <<<JAVASCRIPT
        /**
         * DOES NOT HAVE ATTRIBUTE
         */
        JAVASCRIPT;
        $expectedClass = DefaultMaxLength::class;
        $sut = new ConstructorArgsOneWithValue(MaxLength::class, "MaxLength", DefaultMaxLength::class, ["FAKE_STRING"]);
        $actualInstance = $sut->toToken($givenDocComment)->newInstance();
        $this->assertInstanceOf($expectedClass, $actualInstance);
    }

    /**
     * @test
     */
    public function shouldReturnDefaultFormatWhenAnnotationIsNotConfigured()
    {
        $givenDocComment = <<<JAVASCRIPT
        /**
         * DOES NOT HAVE ATTRIBUTE
         * @var DateTimeInterface
         */
        JAVASCRIPT;
        $expectedClass = DefaultDateFormat::class;
        $sut = new ConstructorArgsOneWithValue(DateFormat::class, "DateFormat", DefaultDateFormat::class, [new DateTimeImmutable()]);
        $actualInstance = $sut->toToken($givenDocComment)->newInstance();
        $this->assertInstanceOf($expectedClass, $actualInstance);
    }
}
