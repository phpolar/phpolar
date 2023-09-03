<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use DateTimeInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\Attributes\AutomaticDateValue
 */
class AutomaticDateValueTest extends TestCase
{
    /**
     * @test
     * @dataProvider Efortmeyer\Polar\Tests\DataProviders\AutomaticDateValueTestData::testCases
     */
    public function shouldReturnCurrentDateTime(DateTimeInterface $expectedDate)
    {
        $sut = new AutomaticDateValue();
        $actualResult = $sut();
        $difference = $expectedDate->diff($actualResult);
        $differenceAsString = $difference->format("%d days %h hours %m minutes");
        $this->assertSame("0 days 0 hours 0 minutes", $differenceAsString);
    }
}
