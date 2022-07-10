<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Stock\Validation\Noop;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Stock\Attributes\NoopValidate
 *
 * @uses \Efortmeyer\Polar\Stock\Validation\Noop
 * @testdox NoopValidate
 */
class NoopValidateTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnNoopValidator()
    {
        $sut = new NoopValidate();
        $noopValidator = $sut();
        $this->assertInstanceOf(Noop::class, $noopValidator);
    }
}
