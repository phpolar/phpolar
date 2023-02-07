<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\Model\ValidationTrait;
use Phpolar\Phpolar\Model\FieldErrorMessageTrait;
use Phpolar\Phpolar\Tests\DataProviders\MaxDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Max::class)]
#[UsesClass(ValidationTrait::class)]
#[UsesClass(FieldErrorMessageTrait::class)]
#[UsesClass(DefaultValidationError::class)]
#[UsesClass(AbstractValidationError::class)]
final class MaxTest extends TestCase
{
    #[Test]
    #[DataProviderExternal(MaxDataProvider::class, "numberBelowMax")]
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

    #[Test]
    #[DataProviderExternal(MaxDataProvider::class, "numberAboveMax")]
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
