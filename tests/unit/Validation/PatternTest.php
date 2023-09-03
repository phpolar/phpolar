<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\Model\ValidationTrait;
use Phpolar\Phpolar\Model\FieldErrorMessageTrait;
use Phpolar\Phpolar\Tests\DataProviders\PatternDataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Validation\Pattern
 * @uses \Phpolar\Phpolar\Model\ValidationTrait
 * @uses \Phpolar\Phpolar\Model\FieldErrorMessageTrait
 * @uses \Phpolar\Phpolar\Validation\DefaultValidationError
 * @uses \Phpolar\Phpolar\Validation\AbstractValidationError
 */
class PatternTest extends TestCase
{
    /**
     * @test
     * @testdox Shall know email is valid based on given pattern
     * @dataProvider \Phpolar\Phpolar\Tests\DataProviders\PatternDataProvider::validEmails()
     */
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

    /**
     * @test
     * @testdox Shall know phone number is valid based on given pattern
     * @dataProvider \Phpolar\Phpolar\Tests\DataProviders\PatternDataProvider::validPhoneNumbers()
     */
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

    /**
     * @test
     * @testdox Shall be invalid if pattern validation is configured but property is not set
     */
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

    /**
     * @test
     * @testdox Shall be invalid if property does not match configured pattern
     * @dataProvider \Phpolar\Phpolar\Tests\DataProviders\PatternDataProvider::invalidEmails()
     */
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
