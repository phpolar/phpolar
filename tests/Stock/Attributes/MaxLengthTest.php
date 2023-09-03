<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * @covers \Phpolar\Phpolar\Stock\Attributes\MaxLength
 *
 * @uses Phpolar\Phpolar\Core\Attributes\Attribute
 * @uses Phpolar\Phpolar\Stock\Validation\MaxLength
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
        $sut = new MaxLength($givenMaxLength);
        $validator = $sut->withValue($givenValue)->__invoke();
        $reflection = new ReflectionProperty($validator, "maxLength");
        $reflection->setAccessible(true);
        $actualValue = $reflection->getValue($validator);
        $this->assertEquals($givenMaxLength, $actualValue);
    }
}
