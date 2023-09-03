<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\Model\ValidationTrait;
use Phpolar\Phpolar\Model\FieldErrorMessageTrait;
use Phpolar\Phpolar\Tests\DataProviders\MinLengthDataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Validation\MinLength
 * @uses \Phpolar\Phpolar\Model\ValidationTrait
 * @uses \Phpolar\Phpolar\Model\FieldErrorMessageTrait
 * @uses \Phpolar\Phpolar\Validation\DefaultValidationError
 * @uses \Phpolar\Phpolar\Validation\AbstractValidationError
 */
class MinLengthTest extends TestCase
{
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\MinLengthDataProvider::strAboveMin()
     */
    public function shallBeValidIfPropIsGteMinLen(string $valAboveMin)
    {
        $sut = new class($valAboveMin)
        {
            use ValidationTrait;

            #[MinLength(MinLengthDataProvider::MIN_LEN)]
            public string $property;

            public function __construct(string $prop)
            {
                $this->property = $prop;
            }
        };

        $this->assertTrue($sut->isValid());
    }

    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\MinLengthDataProvider::strBelowMin()
     */
    public function shallBeInvalidIfPropIsLtMinLen(string $valBelowMin)
    {
        $sut = new class($valBelowMin)
        {
            use ValidationTrait;

            #[MinLength(MinLengthDataProvider::MIN_LEN)]
            public string $property;

            public function __construct(string $prop)
            {
                $this->property = $prop;
            }
        };

        $this->assertFalse($sut->isValid());
    }
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\MinLengthDataProvider::numberAboveMin
     */
    public function shallBeValidIfNumericPropIsGteMinLen(int|float $valAboveMin)
    {
        $sut = new class($valAboveMin)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[MinLength(MinLengthDataProvider::MIN_LEN)]
            public int|float $property;

            public function __construct(int|float $prop)
            {
                $this->property = $prop;
            }
        };

        $this->assertTrue($sut->isValid());
        $this->assertEmpty($sut->getFieldErrorMessage("property"));
    }

    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\MinLengthDataProvider::numberBelowMin
     */
    public function shallBeInvalidIfNumericPropIsLtMinLen(int|float $valBelowMin)
    {
        $sut = new class($valBelowMin)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[MinLength(MinLengthDataProvider::MIN_LEN)]
            public int|float $property;

            public function __construct(int|float $prop)
            {
                $this->property = $prop;
            }
        };

        $this->assertFalse($sut->isValid());
        $this->assertNotEmpty($sut->getFieldErrorMessage("property"));
    }
}
