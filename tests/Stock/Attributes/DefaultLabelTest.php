<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\Attributes\DefaultLabel
 * @testdox DefaultLabel
 */
class DefaultLabelTest extends TestCase
{
    /**
     * @test
     * @dataProvider Efortmeyer\Polar\Tests\DataProviders\DefaultLabelTestData::testCases
     */
    public function shouldReturnStringWithUpperCaseFirstCharacter(string $givenString, string $expectedUpperCaseFirstChar)
    {
        $sut = new DefaultLabel($givenString);
        $actualResult = $sut();
        $this->assertEquals($expectedUpperCaseFirstChar, $actualResult);
    }
}
