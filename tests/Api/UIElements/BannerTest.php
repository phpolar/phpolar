<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\UIElements;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Api\UIElements\Banner
 *
 * @uses \Efortmeyer\Polar\Api\UIElements\SuccessBanner
 */
class BannerTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetStylingWithExpectedText()
    {
        $sut = new ErrorBanner();
        $this->assertMatchesRegularExpression("/background-color(.*?);/", $sut->getStyle());
    }
}
