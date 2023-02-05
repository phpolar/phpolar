<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Validation;

use Phpolar\Phpolar\Core\Validation\DefaultErrorMessages;
use Phpolar\Phpolar\Core\Validation\Exception\ValidatorWithNoErrorMessageException;
use Phpolar\Phpolar\Tests\Stubs\ValidatorWithNoErrorMessage;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Validation\DefaultValidationError
 * @covers \Phpolar\Phpolar\Validation\AbstractValidationError
 * @uses \Phpolar\Phpolar\Validation\Max
 */
final class DefaultValidationErrorTest extends TestCase
{
    /**
     * @test
     * @testdox Shall get the expected error message for the given attribute
     */
    public function a()
    {
        $sut = new DefaultValidationError(new Max(PHP_INT_MAX));
        $this->assertSame(DefaultErrorMessages::Max->value, $sut->getMessage());
    }

    /**
     * @test
     * @testdox Shall throw an exception when the given validator does not have an error message set up
     */
    public function b()
    {
        $sut = new DefaultValidationError(new ValidatorWithNoErrorMessage());
        $this->expectException(ValidatorWithNoErrorMessageException::class);
        $sut->getMessage();
    }
}
