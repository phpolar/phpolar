<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\Model\ValidationTrait;
use Phpolar\Phpolar\Model\FieldErrorMessageTrait;
use Phpolar\Phpolar\Tests\DataProviders\MinLengthDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MinLength::class)]
#[CoversClass(AbstractValidationError::class)]
#[UsesClass(ValidationTrait::class)]
#[UsesClass(FieldErrorMessageTrait::class)]
#[UsesClass(DefaultValidationError::class)]
final class MinLengthTest extends TestCase
{
    #[Test]
    #[DataProviderExternal(MinLengthDataProvider::class, "strAboveMin")]
    public function shallBeValidIfPropIsGteMinLen(string $valAboveMin)
    {
        $sut = new class ($valAboveMin)
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

    #[Test]
    #[DataProviderExternal(MinLengthDataProvider::class, "strBelowMin")]
    public function shallBeInvalidIfPropIsLtMinLen(string $valBelowMin)
    {
        $sut = new class ($valBelowMin)
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

    #[Test]
    #[DataProviderExternal(MinLengthDataProvider::class, "numberAboveMin")]
    public function shallBeValidIfNumericPropIsGteMinLen(int|float $valAboveMin)
    {
        $sut = new class ($valAboveMin)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[MinLength(MinLengthDataProvider::MIN_LEN)]
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

    #[Test]
    #[DataProviderExternal(MinLengthDataProvider::class, "numberBelowMin")]
    public function shallBeInvalidIfNumericPropIsLtMinLen(int|float $valBelowMin)
    {
        $sut = new class ($valBelowMin)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[MinLength(MinLengthDataProvider::MIN_LEN)]
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


    #[TestDox("Shall be valid if property type does not have a length")]
    public function testA()
    {
        $sut = new class (null)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[MinLength(MinLengthDataProvider::MIN_LEN)]
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
