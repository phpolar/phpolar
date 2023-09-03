<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api\UIElements;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Api\UIElements\SuccessBanner
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
