<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Core\Parsers\Annotation;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Core\Parsers\Annotation\Token
 */
class TokenTest extends TestCase
{
    public static function arguments(): array
    {
        return [
            [[]],
            [range(1, 3)],
            [range("a", "g")]
        ];
    }

    public static function names(): array
    {
        return [
            [""],
            [uniqid()],
            [join("", range("a", "g"))]
        ];
    }

    /**
     * @test
     * @dataProvider arguments
     * @testdox getArguments returns the configured value
     */
    public function getArguments__returnsTheConfiguredValue($givenArguments)
    {
        $withArgs = new Token("", $givenArguments);
        $this->assertSame($givenArguments, $withArgs->getArguments());
    }

    /**
     * @test
     * @dataProvider names
     * @testdox getName returns the configured value
     */
    public function getName__returnsTheConfiguredValue(string $givenName)
    {
        $withName = new Token($givenName, []);
        $this->assertSame($givenName, $withName->getName());
    }
}
