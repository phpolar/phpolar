<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Stock\Attributes\DefaultDateFormat
 * @testdox DefaultDateFormat
 */
class DefaultDateFormatTest extends TestCase
{
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\DefaultDateFormatTestData::testCases
     */
    public function shouldReturnFormattedDate(string $expectedFormat)
    {
        $sut = new DefaultDateFormat();
        $actualResult = $sut();
        $this->assertEquals($expectedFormat, $actualResult);
    }
}
