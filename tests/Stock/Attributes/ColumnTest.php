<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Stock\Attributes\Column
 */
class ColumnTest extends TestCase
{
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\ColumnTestData::text
     */
    public function shouldReturnTheGivenText(string $givenText)
    {
        $sut = new Column($givenText);
        $actualResult = $sut();
        $this->assertEquals($givenText, $actualResult);
    }
}
