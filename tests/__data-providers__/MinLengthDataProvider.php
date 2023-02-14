<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

final class MinLengthDataProvider
{
    public const MIN_LEN = 19;

    public const NUM_MAX_LEN = 19 - 1; // PHP_INT_MAX length is 19

    public static function strAboveMin(): array
    {
        return array_map(
            self::getRandomStrAboveMinLenTestCase(...),
            range(0, random_int(1, 20))
        );
    }

    public static function strBelowMin(): array
    {
        return array_map(
            self::getRandomStrBelowMinLenTestCase(...),
            range(0, random_int(1, 20))
        );
    }

    /**
     * @return int[]|float[]
     */
    public static function numberAboveMin(): array
    {
        return array_map(
            self::getRandomNumAboveMinLenTestCase(...),
            range(0, random_int(1, 20))
        );
    }

    /**
     * @return int[]|float[]
     */
    public static function numberBelowMin(): array
    {
        return array_map(
            self::getRandomNumBelowMinLenTestCase(...),
            range(0, random_int(1, 20))
        );
    }

    private static function getRandomStrBelowMinLenTestCase(): array
    {
        return [str_repeat(chr(random_int(32, 126)), self::MIN_LEN - random_int(1, self::MIN_LEN - 1))];
    }

    private static function getRandomStrAboveMinLenTestCase(): array
    {
        return [str_repeat(chr(random_int(32, 126)), self::MIN_LEN + random_int(0, 20))];
    }

    private static function getRandomNumBelowMinLenTestCase(): array
    {
        return [
            random_int((int) substr((string) PHP_INT_MIN, 0, min(self::MIN_LEN - 1, self::NUM_MAX_LEN)), (int) str_repeat("9", min(self::MIN_LEN - 1, self::NUM_MAX_LEN)))
        ];
    }

    private static function getRandomNumAboveMinLenTestCase(): array
    {
        return [
            random_int((int) str_repeat("1", max(self::MIN_LEN, self::NUM_MAX_LEN)), (int) substr((string) PHP_INT_MAX, 0, max(self::MIN_LEN, self::NUM_MAX_LEN)))
        ];
    }
}
