<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\Attributes\Column
 */
class ColumnTest extends TestCase
{
    /**
     * @test
     * @dataProvider Efortmeyer\Polar\Tests\DataProviders\ColumnTestData::text
     */
    public function shouldReturnTheGivenText(string $givenText)
    {
        $sut = new Column($givenText);
        $actualResult = $sut();
        $this->assertEquals($givenText, $actualResult);
    }
}
