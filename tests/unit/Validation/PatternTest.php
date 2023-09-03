<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\Model\ValidationTrait;
use Phpolar\Phpolar\Model\FieldErrorMessageTrait;
use Phpolar\Phpolar\Tests\DataProviders\PatternDataProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Pattern::class)]
#[UsesClass(ValidationTrait::class)]
#[UsesClass(FieldErrorMessageTrait::class)]
#[UsesClass(DefaultValidationError::class)]
#[UsesClass(AbstractValidationError::class)]
final class PatternTest extends TestCase
{
    #[Test]
    #[DataProviderExternal(PatternDataProvider::class, "validEmails")]
    public function shallBeValidEmailBasedOnPattern(string $val)
    {
        $sut = new class($val)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Pattern(PatternDataProvider::EMAIL_PATTERN)]
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
    #[DataProviderExternal(PatternDataProvider::class, "validPhoneNumbers")]
    public function shallBeValidPhoneNumberBasedOnPattern(string $val)
    {
        $sut = new class($val)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Pattern(PatternDataProvider::PHONE_PATTERN)]
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
        $sut = new class()
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Pattern(PatternDataProvider::PHONE_PATTERN)]
            public mixed $property;
        };

        $this->assertFalse($sut->isValid());
        $this->assertNotEmpty($sut->getFieldErrorMessage("property"));
    }

    #[Test]
    #[DataProviderExternal(PatternDataProvider::class, "invalidEmails")]
    public function shallBeInvalidIfPropDoesNotMatchPattern(mixed $val)
    {
        $sut = new class($val)
        {
            use ValidationTrait;
            use FieldErrorMessageTrait;

            #[Pattern(PatternDataProvider::EMAIL_PATTERN)]
            public mixed $property;

            public function __construct(mixed $invalidVal)
            {
                $this->property = $invalidVal;
            }
        };

        $this->assertFalse($sut->isValid());
        $this->assertNotEmpty($sut->getFieldErrorMessage("property"));
    }
}
