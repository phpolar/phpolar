<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\Model\ValidationTrait;
use Phpolar\Phpolar\Model\FieldErrorMessageTrait;
use Phpolar\Phpolar\Core\Validation\DefaultErrorMessages;
use Phpolar\Phpolar\Tests\DataProviders\MaxLengthDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MaxLength::class)]
#[CoversClass(ValidationTrait::class)]
#[CoversClass(AbstractValidationError::class)]
#[UsesClass(FieldErrorMessageTrait::class)]
#[UsesClass(DefaultValidationError::class)]
final class MaxLengthTest extends TestCase
{
    #[Test]
    #[DataProviderExternal(MaxLengthDataProvider::class, "strBelowMax")]
    public function shallBeValidIfPropIsLteMaxLen(string $valBelowMax)
    {
        $sut = new class ($valBelowMax)
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

    #[Test]
    #[DataProviderExternal(MaxLengthDataProvider::class, "strAboveMax")]
    public function shallBeInvalidIfPropIsGtMaxLen(string $valAboveMax)
    {
        $sut = new class ($valAboveMax)
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

    #[Test]
    #[DataProviderExternal(MaxLengthDataProvider::class, "numberBelowMax")]
    public function shallBeValidIfNumericPropIsLteMaxLen(int|float $valBelowMax)
    {
        $sut = new class ($valBelowMax)
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

    #[Test]
    #[DataProviderExternal(MaxLengthDataProvider::class, "numberAboveMax")]
    public function shallBeInvalidIfNumericPropIsGtMaxLen(int|float $valAboveMax)
    {
        $sut = new class ($valAboveMax)
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

    #[TestDox("Shall be valid if property type does not have a length")]
    public function testA()
    {
        $sut = new class (null)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[MaxLength(MaxLengthDataProvider::MAX_LEN)]
            public mixed $property;

            public function __construct(mixed $prop)
            {
                $this->property = $prop;
            }
        };

        $this->assertTrue($sut->isValid());
        $this->assertEmpty($sut->getFieldErrorMessage("property"));
    }
}
