<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\Model\ValidationTrait;
use Phpolar\Phpolar\Model\FieldErrorMessageTrait;
use Phpolar\Phpolar\Tests\DataProviders\RequiredDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Required::class)]
#[UsesClass(ValidationTrait::class)]
#[UsesClass(FieldErrorMessageTrait::class)]
#[UsesClass(DefaultValidationError::class)]
#[UsesClass(AbstractValidationError::class)]
final class RequiredTest extends TestCase
{
    #[Test]
    #[DataProviderExternal(RequiredDataProvider::class, "nonEmptyVals")]
    public function shallBeValidIfPropIsSetWithNonEmptyVal(mixed $val)
    {
        $sut = new class ($val)
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

    #[Test]
    public function shallBeInvalidIfPropIsNotSet()
    {
        $sut = new class ()
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Required]
            public mixed $property;
        };

        $this->assertFalse($sut->isValid());
        $this->assertNotEmpty($sut->getFieldErrorMessage("property"));
    }

    #[Test]
    #[DataProviderExternal(RequiredDataProvider::class, "emptyVals")]
    public function shallBeInvalidIfPropIsEmpty(mixed $emptyVals)
    {
        $sut = new class ($emptyVals)
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
