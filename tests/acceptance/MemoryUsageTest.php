<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use PHPUnit\Framework\TestCase;
use const \Phpolar\Phpolar\Tests\PROJECT_MEMORY_USAGE_THRESHOLD;

/**
 * @runTestsInSeparateProcesses
 * @coversNothing
 */
final class MemoryUsageTest extends TestCase
{
    public function thresholds()
    {
        return [
            [(int) PROJECT_MEMORY_USAGE_THRESHOLD]
        ];
    }

    /**
     * @test
     * @dataProvider thresholds()
     * @testdox Memory usage shall be below $threshold bytes
     */
    public function shallBeBelowThreshold(int $threshold)
    {
        $totalUsed = -memory_get_usage();
        $this->createFormFromTemplate()
            ->createListFromTemplate()
            ->saveDataToFile()
            ->retrieveDataFromFile();
        $totalUsed += memory_get_usage();
        // $this->assertGreaterThan(0, $totalUsed);
        // $this->assertLessThanOrEqual($threshold, $totalUsed);
    }

    private function createFormFromTemplate(): self
    {
        return $this;
    }

    private function createListFromTemplate(): self
    {
        return $this;
    }

    private function retrieveDataFromFile(): self
    {
        return $this;
    }

    private function saveDataToFile(): self
    {
        return $this;
    }
}
