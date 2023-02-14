<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use DateTimeInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Stock\Attributes\DateFormat
 */
class DateFormatTest extends TestCase
{
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\DateFormatTestData::testCases
     */
    public function shouldReturnFormattedDate(string $format)
    {
        $sut = new DateFormat($format);
        $actualResult = $sut();
        $this->assertEquals($format, $actualResult);
    }
}
