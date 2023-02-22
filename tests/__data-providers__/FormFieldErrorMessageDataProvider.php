<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

use Phpolar\Phpolar\Model\AbstractModel;
use Phpolar\Phpolar\Validation\Max;
use Phpolar\Phpolar\Validation\MaxLength;
use Phpolar\Phpolar\Validation\Min;
use Phpolar\Phpolar\Validation\MinLength;
use Phpolar\Phpolar\Validation\Pattern;
use Phpolar\Phpolar\Validation\Required;

final class FormFieldErrorMessageDataProvider
{
    public static function invalidPropertyTestCases()
    {
        yield [
            "Value is greater than the maximum",
            new class () extends AbstractModel
            {
                protected bool $shouldValidate = true;

                #[Max(50)]
                public int $prop = 51;
            }
        ];
        yield [
            "Value is greater than the maximum",
            new class () extends AbstractModel
            {
                protected bool $shouldValidate = true;

                #[Max(50)]
                public int $prop = 51;
            }
        ];
        yield [
            "Maximum length validation failed",
            new class () extends AbstractModel
            {
                protected bool $shouldValidate = true;
                #[MaxLength(10)]
                public string $prop = "9123456780a";
            },
        ];
        yield [
            "Value is less than the minimum",
            new class () extends AbstractModel
            {
                protected bool $shouldValidate = true;
                #[Min(5)]
                public int $prop = 4;
            }
        ];
        yield [
            "Minimum length validation failed",
            new class () extends AbstractModel
            {
                protected bool $shouldValidate = true;
                #[MinLength(10)]
                public string $prop = "123456780";
            },
        ];
        yield [
            "Pattern validation failed",
            new class () extends AbstractModel
            {
                protected bool $shouldValidate = true;
                #[Pattern("/^[[:alnum:]]+$/")]
                public string $prop = "abcd1234$$;%";
            },
        ];
        yield [
            "Required value",
            new class () extends AbstractModel
            {
                protected bool $shouldValidate = true;
                #[Required]
                public string $prop;
            },
        ];
    }

    public static function invalidPropertyNotPostedTestCases()
    {
        yield [
            new class () extends AbstractModel
            {
                protected bool $shouldValidate = false;

                #[Max(50)]
                public int $prop = 51;
            }
        ];
        yield [
            new class () extends AbstractModel
            {
                protected bool $shouldValidate = false;

                #[Max(50)]
                public int $prop = 51;
            }
        ];
        yield [
            new class () extends AbstractModel
            {
                protected bool $shouldValidate = false;
                #[MaxLength(10)]
                public string $prop = "9123456780a";
            },
        ];
        yield [
            new class () extends AbstractModel
            {
                protected bool $shouldValidate = false;
                #[Min(5)]
                public int $prop = 4;
            }
        ];
        yield [
            new class () extends AbstractModel
            {
                protected bool $shouldValidate = false;
                #[MinLength(10)]
                public string $prop = "123456780";
            },
        ];
        yield [
            new class () extends AbstractModel
            {
                protected bool $shouldValidate = false;
                #[Pattern("/^[[:alnum:]]+$/")]
                public string $prop = "abcd1234$$;%";
            },
        ];
        yield [
            new class () extends AbstractModel
            {
                protected bool $shouldValidate = false;
                #[Required]
                public string $prop;
            },
        ];
    }

    public static function validPropertyTestCases()
    {
        yield [
            "",
            new class () extends AbstractModel
            {
                protected bool $shouldValidate = true;
                #[Required]
                public string $prop = "REQUIRED PROP IS SET";
            }
        ];
    }
}
