<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Stock\Attributes\DefaultFormControl
 * @testdox DefaultFormControl
 */
class DefaultFormControlTest extends TestCase
{
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\DefaultFormControlTestData::testCases
     */
    public function shouldReturnStringWithUpperCaseFirstCharacter(string $expectedType)
    {
        $sut = new DefaultFormControl();
        $actualResult = $sut();
        $this->assertEquals($expectedType, $actualResult);
    }
}
