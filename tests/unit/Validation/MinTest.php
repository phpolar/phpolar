<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\ValidationTrait;
use Phpolar\Phpolar\FieldErrorMessageTrait;
use Phpolar\Phpolar\Tests\DataProviders\MinDataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Validation\Min
 * @uses \Phpolar\Phpolar\ValidationTrait
 * @uses \Phpolar\Phpolar\FieldErrorMessageTrait
 * @uses \Phpolar\Phpolar\Validation\DefaultValidationError
 * @uses \Phpolar\Phpolar\Validation\AbstractValidationError
 */
class MinTest extends TestCase
{
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\MinDataProvider::numberAboveMin
     */
    public function shallBeValidIfNumericPropIsLteMax(int|float $valAboveMin)
    {
        $sut = new class($valAboveMin)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Min(MinDataProvider::MIN)]
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
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\MinDataProvider::numberBelowMin
     */
    public function shallBeInvalidIfNumericPropIsGtMax(int|float $valBelowMin)
    {
        $sut = new class($valBelowMin)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Min(MinDataProvider::MIN)]
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
