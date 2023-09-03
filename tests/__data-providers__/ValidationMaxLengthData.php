<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\DataProviders;

class ValidationMaxLengthData
{
    public static function valid()
    {
        return array_map(
            function (int $len) {
                return [str_repeat("a", $len), $len];
            },
            range(1, random_int(5, 10))
        );
    }

    public static function invalid()
    {
        return array_map(
            function ($testCase) {
                ["maxLength" => $maxLength, "oversizeLength" => $oversizeLength] = $testCase;
                return [str_repeat("a", $oversizeLength), $maxLength];
            },
            array_reduce(
                range(1, random_int(5, 10)),
                function (array $acc, int $len): array {
                    return array_merge($acc, [["maxLength" => $len, "oversizeLength" => $len + random_int(1, 20)]]);
                },
                []
            )
        );
    }
}
