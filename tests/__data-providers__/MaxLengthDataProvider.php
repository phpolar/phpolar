<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

final class MaxLengthDataProvider
{
    public const MAX_LEN = 10;

    public const NUM_MAX_LEN = 19 - 1; // PHP_INT_MAX length is 19

    public static function strAboveMax(): array
    {
        return array_map(
            self::getRandomStrAboveMaxLenTestCase(...),
            range(0, random_int(1, 20))
        );
    }

    public static function strBelowMax(): array
    {
        return array_map(
            self::getRandomStrBelowMaxLenTestCase(...),
            range(0, random_int(1, 20))
        );
    }

    /**
     * @return int[]|float[]
     */
    public static function numberAboveMax(): array
    {
        return array_map(
            self::getRandomNumAboveMaxLenTestCase(...),
            range(0, random_int(1, 20))
        );
    }

    /**
     * @return int[]|float[]
     */
    public static function numberBelowMax(): array
    {
        return array_map(
            self::getRandomNumBelowMaxLenTestCase(...),
            range(0, random_int(1, 20))
        );
    }

    private static function getRandomStrBelowMaxLenTestCase(): array
    {
        return [str_repeat(chr(random_int(32, 126)), self::MAX_LEN - random_int(0, self::MAX_LEN - 1))];
    }

    private static function getRandomStrAboveMaxLenTestCase(): array
    {
        return [str_repeat(chr(random_int(32, 126)), self::MAX_LEN + random_int(1, 20))];
    }

    private static function getRandomNumBelowMaxLenTestCase(): array
    {
        return [
            random_int((int) substr((string) PHP_INT_MIN, 0, min(self::MAX_LEN, self::NUM_MAX_LEN)), (int) str_repeat("9", min(self::MAX_LEN, self::NUM_MAX_LEN)))
        ];
    }

    private static function getRandomNumAboveMaxLenTestCase(): array
    {
        return [
            (int) str_repeat("1", self::MAX_LEN + 1),
        ];
    }
}
