<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use DateTimeInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\Attributes\DateFormat
 */
class DateFormatTest extends TestCase
{
    /**
     * @test
     * @dataProvider Efortmeyer\Polar\Tests\DataProviders\DateFormatTestData::testCases
     */
    public function shouldReturnFormattedDate(string $format)
    {
        $sut = new DateFormat($format);
        $actualResult = $sut();
        $this->assertEquals($format, $actualResult);
    }
}
