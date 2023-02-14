<?php

declare(strict_types=1);


namespace Phpolar\Phpolar\Core\Parsers\Annotation;

use Phpolar\Phpolar\Stock\Attributes\NoopValidate;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Core\Parsers\Annotation\ConstructorArgsNone
 * @covers \Phpolar\Phpolar\Core\Parsers\Annotation\Token
 * @covers \Phpolar\Phpolar\Core\Parsers\Annotation\Constructor
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
