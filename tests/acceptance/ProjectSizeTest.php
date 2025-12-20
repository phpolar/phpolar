<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[TestDox("Small Project Size")]
#[CoversNothing]
final class ProjectSizeTest extends TestCase
{
    private const PROJECT_SIZE_THRESHOLD = 19999;
    private const SRC_GLOB = "/src{/,/**/}*.php";

    #[Test]
    #[TestDox("Source code total size shall be below \$threshold bytes")]
    public function shallBeBelowThreshold(int|string $threshold = self::PROJECT_SIZE_THRESHOLD)
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
                        glob(getcwd() . self::SRC_GLOB, GLOB_BRACE),
                    ),
                ),
            )
        );
        $this->assertGreaterThan(0, $totalSize);
        $this->assertLessThanOrEqual($threshold, $totalSize);
    }
}
