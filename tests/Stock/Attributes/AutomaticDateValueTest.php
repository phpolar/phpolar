<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use DateTimeInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Stock\Attributes\AutomaticDateValue
 */
class AutomaticDateValueTest extends TestCase
{
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\AutomaticDateValueTestData::testCases
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
