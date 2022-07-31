<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\UIElements;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Api\UIElements\ErrorBanner
 * @testdox ErrorBanner
 */
class ErrorBannerTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetErrorMessage()
    {
        $sut = new ErrorBanner();
        $this->assertEquals(Messages::ERROR_MESSAGE, $sut->getMessage());
    }
}
