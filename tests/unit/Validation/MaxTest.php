<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\Model\ValidationTrait;
use Phpolar\Phpolar\Model\FieldErrorMessageTrait;
use Phpolar\Phpolar\Tests\DataProviders\MaxDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Max::class)]
#[CoversClass(AbstractValidationError::class)]
#[UsesClass(ValidationTrait::class)]
#[UsesClass(FieldErrorMessageTrait::class)]
#[UsesClass(DefaultValidationError::class)]
final class MaxTest extends TestCase
{
    #[TestDox("Shall be valid if numeric prop is LTE max with value \$valBelowMax")]
    #[DataProviderExternal(MaxDataProvider::class, "numberBelowMax")]
    public function test1(int|float $valBelowMax)
    {
        $sut = new class ($valBelowMax)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Max(MaxDataProvider::MAX)]
            public int|float $property;

            public function __construct(int|float $prop)
            {
                $this->shouldValidate = true;
                $this->property = $prop;
            }
        };

        $this->assertTrue($sut->isValid());
        $this->assertEmpty($sut->getFieldErrorMessage("property"));
    }

    #[TestDox("Shall be invalid if numeric prop is GTE max with value \$valAboveMax")]
    #[DataProviderExternal(MaxDataProvider::class, "numberAboveMax")]
    public function test2(int|float $valAboveMax)
    {
        $sut = new class ($valAboveMax)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Max(MaxDataProvider::MAX)]
            public int|float $property;

            public function __construct(int|float $prop)
            {
                $this->shouldValidate = true;
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

            #[Max(MaxDataProvider::MAX)]
            public mixed $property;

            public function __construct(mixed $prop)
            {
                $this->shouldValidate = true;
                $this->property = $prop;
            }
        };

        $this->assertTrue($sut->isValid());
        $this->assertEmpty($sut->getFieldErrorMessage("property"));
    }
}
