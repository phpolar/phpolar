<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes;

use Efortmeyer\Polar\Stock\Validation\ScalarTypes;

use PHPUnit\Framework\TestCase;

use ReflectionProperty;

/**
 * @covers \Efortmeyer\Polar\Stock\Attributes\TypeValidation
 *
 * @uses \Efortmeyer\Polar\Stock\Validation\TypeValidation
 * @testdox TypeValidation
 */
class TypeValidationTest extends TestCase
{
    /**
     * @test
     */
    public function shouldCreateValidatorWithGivenType()
    {
        $givenValue = str_repeat("a", random_int(1, 100));
        $givenType = ScalarTypes::STRING;
        $sut = new TypeValidation($givenValue, $givenType);
        $validator = $sut();
        $reflection = new ReflectionProperty($validator, "type");
        $reflection->setAccessible(true);
        $actualValue = $reflection->getValue($validator);
        $this->assertEquals($givenType, $actualValue);
    }
}
