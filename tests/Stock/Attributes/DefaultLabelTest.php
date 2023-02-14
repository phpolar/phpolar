<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Stock\Attributes\DefaultLabel
 * @testdox DefaultLabel
 */
class DefaultLabelTest extends TestCase
{
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\DefaultLabelTestData::testCases
     */
    public function shouldReturnStringWithUpperCaseFirstCharacter(string $givenString, string $expectedUpperCaseFirstChar)
    {
        $sut = new DefaultLabel($givenString);
        $actualResult = $sut();
        $this->assertEquals($expectedUpperCaseFirstChar, $actualResult);
    }
}
