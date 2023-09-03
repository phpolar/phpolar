<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Validation;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Stock\Validation\Noop
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
