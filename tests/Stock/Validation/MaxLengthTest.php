<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Validation;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Stock\Validation\MaxLength
 * @testdox MaxLength
 */
class MaxLengthTest extends TestCase
{
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\ValidationMaxLengthData::valid
     */
    public function shouldBeValidWhenValueIsValid($validValue, $givenMaxLength)
    {
        $sut = new MaxLength($validValue, $givenMaxLength);
        $this->assertTrue($sut->isValid());
    }

    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\ValidationMaxLengthData::valid
     */
    public function shouldNotHaveErrorMessageWhenValueIsValid($validValue, $givenMaxLength)
    {
        $sut = new MaxLength($validValue, $givenMaxLength);
        $this->assertEmpty($sut->getErrorMessage());
    }

    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\ValidationMaxLengthData::invalid
     */
    public function shouldBeInvalidWhenValueIsInvalid($invalidValue, $givenMaxLength)
    {
        $sut = new MaxLength($invalidValue, $givenMaxLength);
        $this->assertFalse($sut->isValid());
    }

    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\ValidationMaxLengthData::invalid
     */
    public function shouldHaveErrorMessageWhenValueIsInvalid($invalidValue, $givenMaxLength)
    {
        $sut = new MaxLength($invalidValue, $givenMaxLength);
        $this->assertNotEmpty($sut->getErrorMessage());
    }
}
