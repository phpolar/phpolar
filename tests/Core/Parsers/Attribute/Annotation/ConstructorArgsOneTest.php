<?php

declare(strict_types=1);


namespace Phpolar\Phpolar\Core\Parsers\Annotation;

use Phpolar\Phpolar\Stock\Attributes\DefaultLabel;
use Phpolar\Phpolar\Stock\Attributes\Label;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsOne
 * @covers \Phpolar\Phpolar\Core\Parsers\Annotation\Token
 * @covers \Phpolar\Phpolar\Core\Parsers\Annotation\Constructor
 *
 * @uses \Phpolar\Phpolar\Stock\Attributes\Label
 * @uses \Phpolar\Phpolar\Stock\Attributes\DefaultLabel
 * @testdox ConstructorArgsOne
 */
class ConstructorArgsOneTest extends TestCase
{
    public static function constructorArgTestCases()
    {
        return [
            [
                <<<JAVASCRIPT
                /**
                 * @Label(A B C D)
                 * @Another()
                 */
                JAVASCRIPT,
                "A B C D",
            ],
            [
                <<<JAVASCRIPT
                /**
                 * @Label("A B C D")
                 * @Another()
                 */
                JAVASCRIPT,
                "A B C D",
            ],
            [
                <<<JAVASCRIPT
                /**
                 * @Label('A B C D')
                 * @Another()
                 */
                JAVASCRIPT,
                "A B C D",
            ]
        ];
    }

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
     * @dataProvider constructorArgTestCases
     */
    public function shouldParseStringIntoExpectedAttributeWithExpectedConstructorArgsWhenConstructorArgsAreNotQuotedAndAttributeIsAboveAnotherAttribute(
        string $givenDocComment,
        string $expectedConstructorArgs
    ) {
        $sut = new ConstructorArgsOne(Label::class, "Label", DefaultLabel::class, ["nothing"], "");
        $actualInstance = $sut->toToken($givenDocComment)->newInstance();
        $actualLabelText = $actualInstance();
        $this->assertSame($expectedConstructorArgs, $actualLabelText);
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
