<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Phpolar\Phpolar\Stock\Attributes\Defaults;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * @covers \Phpolar\Phpolar\Stock\Attributes\DefaultMaxLength
 *
 * @uses \Phpolar\Phpolar\Stock\Validation\MaxLength
 * @testdox DefaultMaxLength
 */
class DefaultMaxLengthTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCreateValidatorWithDefaultMaxLength()
    {
        $givenValue = str_repeat("a", random_int(1, 100));
        $sut = new DefaultMaxLength($givenValue);
        $validator = $sut();
        $reflection = new ReflectionProperty($validator, "maxLength");
        $reflection->setAccessible(true);
        $actualValue = $reflection->getValue($validator);
        $this->assertEquals(Defaults::MAX_LENGTH, $actualValue);
    }
}
