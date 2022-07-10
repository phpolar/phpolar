<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\Attributes\Input
 */
class InputTest extends TestCase
{
    /**
     * @test
     * @dataProvider Efortmeyer\Polar\Tests\DataProviders\InputTestData::type
     */
    public function shouldReturnTheGivenText(string $givenType)
    {
        $sut = new Input($givenType);
        $actualResult = $sut();
        $this->assertEquals($givenType, $actualResult);
    }
}
