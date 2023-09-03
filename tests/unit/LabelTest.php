<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Label
 * @covers \Phpolar\Phpolar\LabelFormatTrait
 */
final class LabelTest extends TestCase
{
    /**
     * @test
     * @dataProvider \Phpolar\Phpolar\Tests\DataProviders\LabelDataProvider::getLabelTestCases()
     */
    public function shallReturnFormattedTextUsingPropName(string $expected, string $propName, AbstractModel $sut)
    {
        $this->assertSame($expected, $sut->getLabel($propName));
    }

    /**
     * @test
     * @dataProvider \Phpolar\Phpolar\Tests\DataProviders\LabelDataProvider::getUnconfiguredPropertyTestCases()
     */
    public function shallReturnFormattedTextWhenPropIsNotConfigured(
        string $expected,
        string $propName,
        AbstractModel $sut
    ) {
        $this->assertSame($expected, $sut->getLabel($propName));
    }

    /**
     * @test
     * @dataProvider \Phpolar\Phpolar\Tests\DataProviders\LabelDataProvider::getConfiguredLabelTestCases()
     */
    public function shallReturnConfiguredLabelForProp(string $expected, string $propName, AbstractModel $sut)
    {
        $this->assertSame($expected, $sut->getLabel($propName));
    }
}
