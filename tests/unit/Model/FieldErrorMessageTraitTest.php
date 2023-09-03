<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Model\FieldErrorMessageTrait
 * @uses \Phpolar\Phpolar\Validation\DefaultValidationError
 * @uses \Phpolar\Phpolar\Validation\AbstractValidationError
 * @uses \Phpolar\Phpolar\Validation\Max
 * @uses \Phpolar\Phpolar\Validation\MaxLength
 * @uses \Phpolar\Phpolar\Validation\Min
 * @uses \Phpolar\Phpolar\Validation\MinLength
 * @uses \Phpolar\Phpolar\Validation\Pattern
 * @uses \Phpolar\Phpolar\Validation\Required
 */
final class FieldErrorMessageTraitTest extends TestCase
{
    /**
     * @test
     * @testdox Shall produce expected error message when property validation fails
     * @dataProvider \Phpolar\Phpolar\Tests\DataProviders\FormFieldErrorMessageDataProvider::invalidPropertyTestCases()
     */
    public function a(string $expectedMessage, object $model)
    {
        $fieldName = "prop";
        $this->assertSame($expectedMessage, $model->getFieldErrorMessage($fieldName));
    }

    /**
     * @test
     * @testdox Shall return an empty string when the property is valid
     * @dataProvider \Phpolar\Phpolar\Tests\DataProviders\FormFieldErrorMessageDataProvider::validPropertyTestCases()
     */
    public function b(string $expectedMessage, object $model)
    {
        $fieldName = "prop";
        $this->assertSame($expectedMessage, $model->getFieldErrorMessage($fieldName));
    }
}
