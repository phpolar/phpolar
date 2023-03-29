<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Model;

use Phpolar\Phpolar\Core\Exception\InvalidInputTypeCastToStringException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(InputTypes::class)]
#[CoversClass(InvalidInputTypeCastToStringException::class)]
final class InputTypesTest extends TestCase
{
    public static function dataProvider(): array
    {
        return [
            [
                "checkbox",
                InputTypes::Checkbox,
            ],
            [
                "datetime-local",
                InputTypes::Date,
            ],
            [
                "hidden",
                InputTypes::Hidden,
            ],
            [
                "number",
                InputTypes::Number,
            ],
            [
                "text",
                InputTypes::Text,
            ],
        ];
    }

    #[DataProvider("dataProvider")]
    #[TestDox("Shall convert to expected string")]
    public function testa(string $expected, InputTypes $inputType)
    {
        $this->assertSame($expected, $inputType->asString());
    }

    #[TestDox("Shall throw an exception when attempting to convert an invalid type to string")]
    public function testb()
    {
        $this->expectException(InvalidInputTypeCastToStringException::class);
        $this->expectExceptionMessage("Attempting to cast an invalid input type to string.");
        InputTypes::Invalid->asString();
    }
}
