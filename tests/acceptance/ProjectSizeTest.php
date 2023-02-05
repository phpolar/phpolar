<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use PHPUnit\Framework\TestCase;

use const \Phpolar\Phpolar\Tests\PROJECT_SIZE_THRESHOLD;

/**
 * @runTestsInSeparateProcesses
 * @coversNothing
 */
final class ProjectSizeTest extends TestCase
{
    public function thresholds()
    {
        return [
            [(int) PROJECT_SIZE_THRESHOLD]
        ];
    }

    /**
     * @test
     * @dataProvider thresholds()
     * @testdox Source code total size shall be below $threshold bytes
     */
    public function shallBeBelowThreshold(int $threshold)
    {
        $totalSize = mb_strlen(
            implode(
                preg_replace(
                    [
                        // strip comments
                        "/\/\*\*(.*?)\//s",
                        "/^(.*?)\/\/(.*?)$/s",
                    ],
                    "",
                    array_map(
                        file_get_contents(...),
                        glob(getcwd() . SRC_GLOB, GLOB_BRACE),
                    ),
                ),
            )
        );
        $this->assertGreaterThan(0, $totalSize);
        $this->assertLessThanOrEqual($threshold, $totalSize);
    }
}
