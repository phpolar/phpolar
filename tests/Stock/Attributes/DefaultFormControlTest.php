<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\Attributes\DefaultFormControl
 * @testdox DefaultFormControl
 */
class DefaultFormControlTest extends TestCase
{
    /**
     * @test
     * @dataProvider Efortmeyer\Polar\Tests\DataProviders\DefaultFormControlTestData::testCases
     */
    public function shouldReturnStringWithUpperCaseFirstCharacter(string $expectedType)
    {
        $sut = new DefaultFormControl();
        $actualResult = $sut();
        $this->assertEquals($expectedType, $actualResult);
    }
}
