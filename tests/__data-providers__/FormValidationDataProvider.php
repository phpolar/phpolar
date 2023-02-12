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

final class FormValidationDataProvider
{
    public static function getTestCases()
    {
        return [
            [
                true,
                Max::class,
                new class () extends AbstractModel
                {
                    #[Max(50)]
                    public int $prop = 50;
                }
            ],
            [
                false,
                Max::class,
                new class () extends AbstractModel
                {
                    #[Max(50)]
                    public int $prop = 51;
                }
            ],
            [
                true,
                MaxLength::class,
                new class () extends AbstractModel
                {
                    #[MaxLength(10)]
                    public string $prop = "9012345678";
                },
            ],
            [
                false,
                MaxLength::class,
                new class () extends AbstractModel
                {
                    #[MaxLength(10)]
                    public string $prop = "9123456780a";
                },
            ],
            [
                true,
                Min::class,
                new class () extends AbstractModel
                {
                    #[Min(5)]
                    public int $prop = 5;
                }
            ],
            [
                false,
                Min::class,
                new class () extends AbstractModel
                {
                    #[Min(5)]
                    public int $prop = 4;
                }
            ],
            [
                true,
                MinLength::class,
                new class () extends AbstractModel
                {
                    #[MinLength(10)]
                    public string $prop = "9812345670";
                },
            ],
            [
                false,
                MinLength::class,
                new class () extends AbstractModel
                {
                    #[MinLength(10)]
                    public string $prop = "123456780";
                },
            ],
            [
                true,
                Pattern::class,
                new class () extends AbstractModel
                {
                    #[Pattern("/^[[:alpha:]]+$/")]
                    public string $prop = "abc";
                },
            ],
            [
                false,
                Pattern::class,
                new class () extends AbstractModel
                {
                    #[Pattern("/^[[:alnum:]]+$/")]
                    public string $prop = "abcd1234$$;%";
                },
            ],
            [
                true,
                Pattern::class,
                new class () extends AbstractModel
                {
                    #[Required]
                    public string $prop = "abc";
                },
            ],
            [
                false,
                Pattern::class,
                new class () extends AbstractModel
                {
                    #[Required]
                    public string $prop;
                },
            ],
        ];
    }
}
