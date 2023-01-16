<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api\UIElements;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Api\UIElements\Banner
 *
 * @uses \Phpolar\Phpolar\Api\UIElements\SuccessBanner
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
