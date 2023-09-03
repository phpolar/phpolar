<?php

declare(strict_types=1);


namespace Phpolar\Phpolar\Core\Parsers\Annotation;

use DateTimeImmutable;
use DateTimeInterface;
use Phpolar\Phpolar\Stock\Attributes\DateFormat;
use Phpolar\Phpolar\Stock\Attributes\DefaultDateFormat;
use Phpolar\Phpolar\Stock\Attributes\DefaultLabel;
use Phpolar\Phpolar\Stock\Attributes\DefaultMaxLength;
use Phpolar\Phpolar\Stock\Attributes\Label;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsOneWithValue
 * @covers \Phpolar\Phpolar\Core\Parsers\Annotation\Token
 * @covers \Phpolar\Phpolar\Core\Parsers\Annotation\Constructor
 *
 * @uses \Phpolar\Phpolar\Stock\Attributes\Label
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultLabel
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultDateFormat
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultMaxLength
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
