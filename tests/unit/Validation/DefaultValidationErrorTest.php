<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\Core\Validation\DefaultErrorMessages;
use Phpolar\Phpolar\Core\Validation\Exception\ValidatorWithNoErrorMessageException;
use Phpolar\Phpolar\Tests\Stubs\ValidatorWithNoErrorMessage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(DefaultValidationError::class)]
#[CoversClass(AbstractValidationError::class)]
#[UsesClass(Max::class)]
final class DefaultValidationErrorTest extends TestCase
{
    #[TestDox("Shall get the expected error message for the given attribute")]
    public function test1()
    {
        $sut = new DefaultValidationError(new Max(PHP_INT_MAX));
        $this->assertSame(DefaultErrorMessages::Max->value, $sut->getMessage());
    }

    #[TestDox("Shall throw an exception when the given validator does not have an error message set up")]
    public function test2()
    {
        $sut = new DefaultValidationError(new ValidatorWithNoErrorMessage());
        $this->expectException(ValidatorWithNoErrorMessageException::class);
        $sut->getMessage();
    }
}
