<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\Model\ValidationTrait;
use Phpolar\Phpolar\Model\FieldErrorMessageTrait;
use Phpolar\Phpolar\Tests\DataProviders\MinDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Min::class)]
#[CoversClass(AbstractValidationError::class)]
#[UsesClass(ValidationTrait::class)]
#[UsesClass(FieldErrorMessageTrait::class)]
#[UsesClass(DefaultValidationError::class)]
class MinTest extends TestCase
{
    #[Test]
    #[DataProviderExternal(MinDataProvider::class, "numberAboveMin")]
    public function shallBeValidIfNumericPropIsLteMax(int|float $valAboveMin)
    {
        $sut = new class ($valAboveMin)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Min(MinDataProvider::MIN)]
            public int|float $property;

            public function __construct(int|float $prop)
            {
                $this->isPosted = true;
                $this->property = $prop;
            }
        };

        $this->assertTrue($sut->isValid());
        $this->assertEmpty($sut->getFieldErrorMessage("property"));
    }

    #[Test]
    #[DataProviderExternal(MinDataProvider::class, "numberBelowMin")]
    public function shallBeInvalidIfNumericPropIsGtMax(int|float $valBelowMin)
    {
        $sut = new class ($valBelowMin)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Min(MinDataProvider::MIN)]
            public int|float $property;

            public function __construct(int|float $prop)
            {
                $this->isPosted = true;
                $this->property = $prop;
            }
        };

        $this->assertFalse($sut->isValid());
        $this->assertNotEmpty($sut->getFieldErrorMessage("property"));
    }

    #[TestDox("Shall be valid if property type is non-numeric")]
    public function testA()
    {
        $sut = new class (null)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Min(MinDataProvider::MIN)]
            public mixed $property;

            public function __construct(mixed $prop)
            {
                $this->isPosted = true;
                $this->property = $prop;
            }
        };

        $this->assertTrue($sut->isValid());
        $this->assertEmpty($sut->getFieldErrorMessage("property"));
    }
}
