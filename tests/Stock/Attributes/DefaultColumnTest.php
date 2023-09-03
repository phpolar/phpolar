<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\Attributes\DefaultColumn
 */
class DefaultColumnTest extends TestCase
{
    /**
     * @test
     * @dataProvider Efortmeyer\Polar\Tests\DataProviders\DefaultColumnTestData::testCases
     */
    public function shouldReturnStringWithUpperCaseFirstCharacter(string $givenString, string $expectedUpperCaseFirstChar)
    {
        $sut = new DefaultColumn($givenString);
        $actualResult = $sut();
        $this->assertEquals($expectedUpperCaseFirstChar, $actualResult);
    }
}
