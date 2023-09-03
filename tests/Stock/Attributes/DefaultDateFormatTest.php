<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use DateTimeInterface;
use Efortmeyer\Polar\Core\Defaults;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\Attributes\DefaultDateFormat
 * @testdox DefaultDateFormat
 */
class DefaultDateFormatTest extends TestCase
{
    /**
     * @test
     * @dataProvider Efortmeyer\Polar\Tests\DataProviders\DefaultDateFormatTestData::testCases
     */
    public function shouldReturnFormattedDate(string $expectedFormat)
    {
        $sut = new DefaultDateFormat();
        $actualResult = $sut();
        $this->assertEquals($expectedFormat, $actualResult);
    }
}
