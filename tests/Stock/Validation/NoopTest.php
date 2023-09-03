<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Validation;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\Validation\Noop
 */
class NoopTest extends TestCase
{
    /**
     * @test
     */
    public function shouldBeValid()
    {
        $sut = new Noop();
        $this->assertTrue($sut->isValid());
    }

    /**
     * @test
     */
    public function shouldNotHaveErrorMessage()
    {
        $sut = new Noop();
        $this->assertEmpty($sut->getErrorMessage());
    }
}
