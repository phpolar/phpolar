<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\Attributes\None
 */
class NoneTest extends TestCase
{
    /**
     * @test
     */
    public function shouldDoNothingWhenInvoked()
    {
        $sut = new None();
        $result = $sut();
        $this->assertNull($result);
    }
}
