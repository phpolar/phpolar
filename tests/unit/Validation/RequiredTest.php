<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\ValidationTrait;
use Phpolar\Phpolar\FieldErrorMessageTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Validation\Required
 * @uses \Phpolar\Phpolar\ValidationTrait
 * @uses \Phpolar\Phpolar\FieldErrorMessageTrait
 * @uses \Phpolar\Phpolar\Validation\DefaultValidationError
 * @uses \Phpolar\Phpolar\Validation\AbstractValidationError
 */
class RequiredTest extends TestCase
{
    /**
     * @test
     * @dataProvider \Phpolar\Phpolar\Tests\DataProviders\RequiredDataProvider::nonEmptyVals()
     */
    public function shallBeValidIfPropIsSetWithNonEmptyVal(mixed $val)
    {
        $sut = new class($val)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Required]
            public mixed $property;

            public function __construct(mixed $prop)
            {
                $this->property = $prop;
            }
        };

        $this->assertTrue($sut->isValid());
        $this->assertEmpty($sut->getFieldErrorMessage("property"));
    }

    /**
     * @test
     */
    public function shallBeInvalidIfPropIsNotSet()
    {
        $sut = new class()
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Required]
            public mixed $property;
        };

        $this->assertFalse($sut->isValid());
        $this->assertNotEmpty($sut->getFieldErrorMessage("property"));
    }

    /**
     * @test
     * @dataProvider \Phpolar\Phpolar\Tests\DataProviders\RequiredDataProvider::emptyVals()
     */
    public function shallBeInvalidIfPropIsEmpty(mixed $emptyVals)
    {
        $sut = new class($emptyVals)
        {
            use ValidationTrait;

            #[Required]
            public mixed $property;

            public function __construct(mixed $emptyVals)
            {
                $this->property = $emptyVals;
            }
        };

        $this->assertFalse($sut->isValid());
    }
}
