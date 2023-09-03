<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\Model\ValidationTrait;
use Phpolar\Phpolar\Model\FieldErrorMessageTrait;
use Phpolar\Phpolar\Core\Validation\DefaultErrorMessages;
use Phpolar\Phpolar\Tests\DataProviders\MaxLengthDataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Validation\MaxLength
 * @covers \Phpolar\Phpolar\Model\ValidationTrait
 * @uses \Phpolar\Phpolar\Model\FieldErrorMessageTrait
 * @uses \Phpolar\Phpolar\Validation\DefaultValidationError
 * @uses \Phpolar\Phpolar\Validation\AbstractValidationError
 */
class MaxLengthTest extends TestCase
{
    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\MaxLengthDataProvider::strBelowMax()
     */
    public function shallBeValidIfPropIsLteMaxLen(string $valBelowMax)
    {
        $sut = new class($valBelowMax)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[MaxLength(MaxLengthDataProvider::MAX_LEN)]
            public string $property;

            public function __construct(string $prop)
            {
                $this->property = $prop;
            }
        };

        $this->assertTrue($sut->isValid());
        $this->assertEmpty($sut->getFieldErrorMessage("property"));
    }

    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\MaxLengthDataProvider::strAboveMax()
     */
    public function shallBeInvalidIfPropIsGtMaxLen(string $valAboveMax)
    {
        $sut = new class($valAboveMax)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[MaxLength(MaxLengthDataProvider::MAX_LEN)]
            public string $property;

            public function __construct(string $prop)
            {
                $this->property = $prop;
            }
        };

        $this->assertFalse($sut->isValid());
        $this->assertNotEmpty($sut->getFieldErrorMessage("property"));
    }

    /**
     * @test
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\MaxLengthDataProvider::numberBelowMax()
     */
    public function shallBeValidIfNumericPropIsLteMaxLen(int|float $valBelowMax)
    {
        $sut = new class($valBelowMax)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[MaxLength(MaxLengthDataProvider::MAX_LEN)]
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
     * @dataProvider Phpolar\Phpolar\Tests\DataProviders\MaxLengthDataProvider::numberAboveMax()
     */
    public function shallBeInvalidIfNumericPropIsGtMaxLen(int|float $valAboveMax)
    {
        $sut = new class($valAboveMax)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[MaxLength(MaxLengthDataProvider::MAX_LEN)]
            public int|float $property;

            public function __construct(int|float $prop)
            {
                $this->property = $prop;
            }
        };

        $this->assertFalse($sut->isValid());
        $this->assertSame(DefaultErrorMessages::MaxLength->value, $sut->getFieldErrorMessage("property"));
    }
}
