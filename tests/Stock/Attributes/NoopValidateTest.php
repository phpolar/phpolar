<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Phpolar\Phpolar\Stock\Validation\Noop;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Stock\Attributes\NoopValidate
 *
 * @uses \Phpolar\Phpolar\Stock\Validation\Noop
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
