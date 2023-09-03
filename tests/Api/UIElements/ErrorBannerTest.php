<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\UIElements;

use Efortmeyer\Polar\Core\Defaults;
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
        $this->assertEquals(Defaults::ERROR_MESSAGE, $sut->getMessage());
    }
}
