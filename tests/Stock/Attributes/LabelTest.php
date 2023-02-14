<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Stock\Attributes\Label
 */
class LabelTest extends TestCase
{
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\LabelTestData::text
     */
    public function shouldReturnTheGivenText(string $givenText)
    {
        $sut = new Label($givenText);
        $actualResult = $sut();
        $this->assertEquals($givenText, $actualResult);
    }
}
