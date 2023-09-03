<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Phpolar\Phpolar\Tests\DataProviders\FormFieldErrorMessageDataProvider;
use Phpolar\Phpolar\Validation\AbstractValidationError;
use Phpolar\Phpolar\Validation\DefaultValidationError;
use Phpolar\Phpolar\Validation\Max;
use Phpolar\Phpolar\Validation\MaxLength;
use Phpolar\Phpolar\Validation\Min;
use Phpolar\Phpolar\Validation\MinLength;
use Phpolar\Phpolar\Validation\Pattern;
use Phpolar\Phpolar\Validation\Required;
use PHPUnit\Framework\Attributes\BackupGlobals;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FieldErrorMessageTrait::class)]
#[CoversFunction("\\Phpolar\\Phpolar\\Validation\\Functions\\getValidationAttributes")]
#[UsesClass(DefaultValidationError::class)]
#[UsesClass(AbstractValidationError::class)]
#[UsesClass(Max::class)]
#[UsesClass(MaxLength::class)]
#[UsesClass(Min::class)]
#[UsesClass(MinLength::class)]
#[UsesClass(Pattern::class)]
#[UsesClass(Required::class)]
final class FieldErrorMessageTraitTest extends TestCase
{
    #[TestDox("Shall produce expected error message when property validation fails")]
    #[DataProviderExternal(FormFieldErrorMessageDataProvider::class, "invalidPropertyTestCases")]
    public function test1(string $expectedMessage, object $model)
    {
        $fieldName = "prop";
        $this->assertSame($expectedMessage, $model->getFieldErrorMessage($fieldName));
    }

    #[TestDox("Shall return an empty string when the property is valid")]
    #[DataProviderExternal(FormFieldErrorMessageDataProvider::class, "validPropertyTestCases")]
    public function test2(string $expectedMessage, object $model)
    {
        $fieldName = "prop";
        $this->assertSame($expectedMessage, $model->getFieldErrorMessage($fieldName));
    }

    #[TestDox("Shall return true when property validation fails")]
    #[DataProviderExternal(FormFieldErrorMessageDataProvider::class, "invalidPropertyTestCases")]
    public function test3(string $expectedMessage, object $model)
    {
        $fieldName = "prop";
        $this->assertTrue($model->hasError($fieldName));
    }

    #[TestDox("Shall return false when the property is valid")]
    #[DataProviderExternal(FormFieldErrorMessageDataProvider::class, "validPropertyTestCases")]
    public function test4(string $expectedMessage, object $model)
    {
        $fieldName = "prop";
        $this->assertFalse($model->hasError($fieldName));
    }

    #[TestDox("Shall not have errors when the model is not posted/submitted")]
    #[DataProviderExternal(FormFieldErrorMessageDataProvider::class, "invalidPropertyNotPostedTestCases")]
    public function test5(object $model)
    {
        $fieldName = "prop";
        $this->assertFalse($model->hasError($fieldName));
    }

    #[TestDox("Shall not have error messages when the model is not posted/submitted")]
    #[DataProviderExternal(FormFieldErrorMessageDataProvider::class, "invalidPropertyNotPostedTestCases")]
    public function test6(object $model)
    {
        $fieldName = "prop";
        $this->assertEmpty($model->getFieldErrorMessage($fieldName));
    }
}
