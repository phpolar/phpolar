<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\Attributes\Label
 */
class LabelTest extends TestCase
{
    /**
     * @test
     * @dataProvider Efortmeyer\Polar\Tests\DataProviders\LabelTestData::text
     */
    public function shouldReturnTheGivenText(string $givenText)
    {
        $sut = new Label($givenText);
        $actualResult = $sut();
        $this->assertEquals($givenText, $actualResult);
    }
}
