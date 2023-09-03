<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\Model\ValidationTrait;
use Phpolar\Phpolar\Model\FieldErrorMessageTrait;
use Phpolar\Phpolar\Tests\DataProviders\MaxDataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Validation\Max
 * @uses \Phpolar\Phpolar\Model\ValidationTrait
 * @uses \Phpolar\Phpolar\Model\FieldErrorMessageTrait
 * @uses \Phpolar\Phpolar\Validation\DefaultValidationError
 * @uses \Phpolar\Phpolar\Validation\AbstractValidationError
 */
class MaxTest extends TestCase
{
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\MaxDataProvider::numberBelowMax()
     */
    public function shallBeValidIfNumericPropIsLteMax(int|float $valBelowMax)
    {
        $sut = new class($valBelowMax)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Max(MaxDataProvider::MAX)]
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
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\MaxDataProvider::numberAboveMax()
     */
    public function shallBeInvalidIfNumericPropIsGtMax(int|float $valAboveMax)
    {
        $sut = new class($valAboveMax)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Max(MaxDataProvider::MAX)]
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
