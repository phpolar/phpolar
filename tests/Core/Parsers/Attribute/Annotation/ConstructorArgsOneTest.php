<?php

declare(strict_types=1);


namespace Efortmeyer\Polar\Core\Parsers\Annotation;

use Efortmeyer\Polar\Stock\Attributes\DefaultLabel;
use Efortmeyer\Polar\Stock\Attributes\Label;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsOne
 * @covers \Efortmeyer\Polar\Core\Parsers\Annotation\Token
 * @covers \Efortmeyer\Polar\Core\Parsers\Annotation\Constructor
 *
 * @uses \Efortmeyer\Polar\Stock\Attributes\Label
 * @uses \Efortmeyer\Polar\Stock\Attributes\DefaultLabel
 * @testdox ConstructorArgsOne
 */
class ConstructorArgsOneTest extends TestCase
{
    /**
     * @test
     */
    public function shouldParseStringIntoExpectedAttribute()
    {
        $givenDocComment = <<<JAVASCRIPT
        /**
         * @Label(A B C D)
         */
        JAVASCRIPT;
        $expectedClass = Label::class;
        $sut = new ConstructorArgsOne(Label::class, "Label", DefaultLabel::class, ["nothing"], "");
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
        $expectedClass = DefaultLabel::class;
        $sut = new ConstructorArgsOne(Label::class, "Label", DefaultLabel::class, ["nothing"]);
        $actualInstance = $sut->toToken($givenDocComment)->newInstance();
        $this->assertInstanceOf($expectedClass, $actualInstance);
    }
}
