<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\UIElements;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Api\UIElements\SuccessBanner
 * @testdox SuccessBanner
 */
class SuccessBannerTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetSuccessMessageWithExpectedText()
    {
        $sut = new SuccessBanner();
        $this->assertEquals(Messages::SUCCESS_MESSAGE, $sut->getMessage());
    }
}
