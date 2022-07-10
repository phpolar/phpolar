<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * @covers \Efortmeyer\Polar\Stock\Attributes\MaxLength
 *
 * @uses \Efortmeyer\Polar\Stock\Validation\MaxLength
 * @testdox MaxLength
 */
class MaxLengthTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCreateValidatorWithGivenMaxLength()
    {
        $givenValue = str_repeat("a", random_int(1, 100));
        $givenMaxLength = strlen($givenValue);
        $sut = new MaxLength($givenValue, $givenMaxLength);
        $validator = $sut();
        $reflection = new ReflectionProperty($validator, "maxLength");
        $reflection->setAccessible(true);
        $actualValue = $reflection->getValue($validator);
        $this->assertEquals($givenMaxLength, $actualValue);
    }
}
