<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes;

use Phpolar\Phpolar\Stock\Validation\ScalarTypes;

use PHPUnit\Framework\TestCase;

use ReflectionProperty;

/**
 * @covers \Phpolar\Phpolar\Stock\Attributes\TypeValidation
 *
 * @uses \Phpolar\Phpolar\Stock\Validation\TypeValidation
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
        $givenType = ScalarTypes::String->value;
        $sut = new TypeValidation($givenValue, $givenType);
        $validator = $sut();
        $reflection = new ReflectionProperty($validator, "type");
        $reflection->setAccessible(true);
        $actualValue = $reflection->getValue($validator);
        $this->assertEquals($givenType, $actualValue);
    }
}
