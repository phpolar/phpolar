<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Phpolar\Phpolar\Tests\Stubs\InvalidPropertyStub;
use Phpolar\Phpolar\Tests\Stubs\ValidPropertyStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(FieldErrorMessageTrait::class)]
final class FieldErrorMessageTraitTest extends TestCase
{
    #[TestDox("Shall produce expected error message when property validation fails")]
    public function test1()
    {
        $model = new class () {
            use FieldErrorMessageTrait;

            #[InvalidPropertyStub]
            public string $prop = "must be set";
        };
        $model->isPosted();
        $fieldName = "prop";
        $this->assertSame(InvalidPropertyStub::EXPECTED_MESSAGE, $model->getFieldErrorMessage($fieldName));
    }

    #[TestDox("Shall return an empty string when the property is valid")]
    public function test2()
    {
        $model = new class () {
            use FieldErrorMessageTrait;

            #[ValidPropertyStub]
            public string $prop = "must be set";
        };
        $model->isPosted();
        $fieldName = "prop";
        $this->assertEmpty($model->getFieldErrorMessage($fieldName));
    }

    #[TestDox("Shall return true when property validation fails")]
    public function test3()
    {
        $model = new class () {
            use FieldErrorMessageTrait;

            #[InvalidPropertyStub]
            public string $prop = "must be set";
        };
        $model->isPosted();
        $fieldName = "prop";
        $this->assertTrue($model->hasError($fieldName));
    }

    #[TestDox("Shall return false when the property is valid")]
    public function test4()
    {
        $model = new class () {
            use FieldErrorMessageTrait;

            #[ValidPropertyStub]
            public string $prop;
        };
        $model->isPosted();
        $fieldName = "prop";
        $this->assertFalse($model->hasError($fieldName));
    }

    #[TestDox("Shall not have errors when the model is not posted/submitted")]
    public function test5b()
    {
        $model = new class () {
            use FieldErrorMessageTrait;

            #[InvalidPropertyStub]
            public string $prop;
        };
        $fieldName = "prop";
        $this->assertFalse($model->hasError($fieldName));
    }

    #[TestDox("Shall not have error messages when the model is not posted/submitted")]
    public function test6()
    {
        $model = new class () {
            use FieldErrorMessageTrait;

            #[InvalidPropertyStub]
            public string $prop;
        };
        $fieldName = "prop";
        $this->assertEmpty($model->getFieldErrorMessage($fieldName));
    }

    #[TestDox("Shall return an empty string if the form has not been posted")]
    public function test7()
    {
        $model = new class () {
            use FieldErrorMessageTrait;
        };
        $fieldName = "prop";
        $this->assertEmpty($model->selectValAttr($fieldName, "INVALID", "VALID"));
    }
}
