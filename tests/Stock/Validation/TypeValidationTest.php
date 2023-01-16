<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Validation;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Stock\Validation\TypeValidation
 * @testdox TypeValidation
 */
class TypeValidationTest extends TestCase
{
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\TypeValidationData::valid
     */
    public function shouldBeValidWhenValueIsValid($validValue, $givenTypeValidation)
    {
        $sut = new TypeValidation($validValue, $givenTypeValidation);
        $this->assertTrue($sut->isValid());
    }

    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\TypeValidationData::valid
     */
    public function shouldNotHaveErrorMessageWhenValueIsValid($validValue, $givenTypeValidation)
    {
        $sut = new TypeValidation($validValue, $givenTypeValidation);
        $this->assertEmpty($sut->getErrorMessage());
    }

    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\TypeValidationData::invalid
     */
    public function shouldBeInvalidWhenValueIsInvalid($invalidValue, $givenTypeValidation)
    {
        $sut = new TypeValidation($invalidValue, $givenTypeValidation);
        $this->assertFalse($sut->isValid());
    }

    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\TypeValidationData::invalid
     */
    public function shouldHaveErrorMessageWhenValueIsInvalid($invalidValue, $givenTypeValidation)
    {
        $sut = new TypeValidation($invalidValue, $givenTypeValidation);
        $this->assertNotEmpty($sut->getErrorMessage());
    }
}
