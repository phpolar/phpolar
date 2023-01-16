<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Stock\Attributes\DefaultColumn
 */
class DefaultColumnTest extends TestCase
{
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\DefaultColumnTestData::testCases
     */
    public function shouldReturnStringWithUpperCaseFirstCharacter(string $givenString, string $expectedUpperCaseFirstChar)
    {
        $sut = new DefaultColumn($givenString);
        $actualResult = $sut();
        $this->assertEquals($expectedUpperCaseFirstChar, $actualResult);
    }
}
