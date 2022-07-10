<?php

declare(strict_types=1);


namespace Efortmeyer\Polar\Core\Parsers\Annotation;

use Efortmeyer\Polar\Stock\Attributes\NoopValidate;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Core\Parsers\Annotation\ConstructorArgsNone
 * @covers \Efortmeyer\Polar\Core\Parsers\Annotation\Token
 * @covers \Efortmeyer\Polar\Core\Parsers\Annotation\Constructor
 *
 * @testdox ConstructorArgsNone
 */
class ConstructorArgsNoneTest extends TestCase
{
    /**
     * @test
     */
    public function shouldParseStringIntoExpectedAttribute()
    {
        $givenDocComment = <<<JAVASCRIPT
        /**
         * @NoopValidate
         */
        JAVASCRIPT;
        $expectedClass = NoopValidate::class;
        $sut = new ConstructorArgsNone(NoopValidate::class, "NoopValidate", NoopValidate::class, []);
        $actualInstance = $sut->toToken($givenDocComment)->newInstance();
        $this->assertInstanceOf($expectedClass, $actualInstance);
    }
}
