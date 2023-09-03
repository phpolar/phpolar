<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

use Phpolar\Phpolar\AbstractModel;
use Phpolar\Phpolar\Validation\Max;
use Phpolar\Phpolar\Validation\MaxLength;
use Phpolar\Phpolar\Validation\Min;
use Phpolar\Phpolar\Validation\MinLength;
use Phpolar\Phpolar\Validation\Pattern;
use Phpolar\Phpolar\Validation\Required;

final class FormFieldErrorMessageDataProvider
{
    public function invalidPropertyTestCases()
    {
        yield [
            "Value is greater than the maximum",
            new class() extends AbstractModel
            {
                #[Max(50)]
                public int $prop = 51;
            }
        ];
        yield [
            "Value is greater than the maximum",
            new class() extends AbstractModel
            {
                #[Max(50)]
                public int $prop = 51;
            }
        ];
        yield [
            "Maximum length validation failed",
            new class() extends AbstractModel
            {
                #[MaxLength(10)]
                public string $prop = "9123456780a";
            },
        ];
        yield [
            "Value is less than the minimum",
            new class() extends AbstractModel
            {
                #[Min(5)]
                public int $prop = 4;
            }
        ];
        yield [
            "Minimum length validation failed",
            new class() extends AbstractModel
            {
                #[MinLength(10)]
                public string $prop = "123456780";
            },
        ];
        yield [
            "Pattern validation failed",
            new class() extends AbstractModel
            {
                #[Pattern("/^[[:alnum:]]+$/")]
                public string $prop = "abcd1234$$;%";
            },
        ];
        yield [
            "Required value",
            new class() extends AbstractModel
            {
                #[Required]
                public string $prop;
            },
        ];
    }

    public function validPropertyTestCases()
    {
        yield [
            "",
            new class() extends AbstractModel
            {
                #[Required]
                public string $prop = "REQUIRED PROP IS SET";
            }
        ];
    }
}
