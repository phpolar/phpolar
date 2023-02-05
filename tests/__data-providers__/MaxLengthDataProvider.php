<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\DataProviders;

final class MaxLengthDataProvider
{
    public const MAX_LEN = 10;

    public const NUM_MAX_LEN = 19 - 1; // PHP_INT_MAX length is 19

    public function strAboveMax(): array
    {
        return array_map(
            self::getRandomStrAboveMaxLenTestCase(...),
            range(0, random_int(1, 20))
        );
    }

    public function strBelowMax(): array
    {
        return array_map(
            self::getRandomStrBelowMaxLenTestCase(...),
            range(0, random_int(1, 20))
        );
    }

    /**
     * @return int[]|float[]
     */
    public function numberAboveMax(): array
    {
        return array_map(
            self::getRandomNumAboveMaxLenTestCase(...),
            range(0, random_int(1, 20))
        );
    }

    /**
     * @return int[]|float[]
     */
    public function numberBelowMax(): array
    {
        return array_map(
            self::getRandomNumBelowMaxLenTestCase(...),
            range(0, random_int(1, 20))
        );
    }

    private function getRandomStrBelowMaxLenTestCase(): array
    {
        return [str_repeat(chr(random_int(32, 126)), self::MAX_LEN - random_int(0, self::MAX_LEN - 1))];
    }

    private function getRandomStrAboveMaxLenTestCase(): array
    {
        return [str_repeat(chr(random_int(32, 126)), self::MAX_LEN + random_int(1, 20))];
    }

    private function getRandomNumBelowMaxLenTestCase(): array
    {
        return [
            random_int((int) substr((string) PHP_INT_MIN, 0, min(self::MAX_LEN, self::NUM_MAX_LEN)), (int) str_repeat("9", min(self::MAX_LEN, self::NUM_MAX_LEN)))
        ];
    }

    private function getRandomNumAboveMaxLenTestCase(): array
    {
        return [
            random_int((int) str_repeat("1", max(self::MAX_LEN + 1, self::NUM_MAX_LEN)), (int) substr((string) PHP_INT_MAX, 0, max(self::MAX_LEN + 1, self::NUM_MAX_LEN)))
        ];
    }
}
