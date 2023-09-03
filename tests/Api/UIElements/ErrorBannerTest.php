<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api\UIElements;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Api\UIElements\ErrorBanner
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
