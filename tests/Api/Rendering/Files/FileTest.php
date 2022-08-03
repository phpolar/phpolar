<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\Rendering\Files;

use Efortmeyer\Polar\Tests\Mocks\IcoFileMock;
use Efortmeyer\Polar\Tests\Extensions\PolarTestCaseExtension;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Api\Rendering\Files\File
 *
 * @uses \Efortmeyer\Polar\Api\Rendering\Files\IcoFile
 */
class FileTest extends PolarTestCaseExtension
{
    /**
     * @var resource
     */
    protected $tempFile;

    protected static $testFileName;

    protected function setUp(): void
    {
        self::$testFileName = self::getTestFileName(".ico");
        $this->tempFile = fopen(self::$testFileName, "c+");
    }

    protected function tearDown(): void
    {
        fclose($this->tempFile);
        unlink(self::$testFileName);
    }

    /**
     * @test
     */
    public function shouldRenderFileContents()
    {
        $fakeIcoFileData = str_repeat("FAKE ", random_int(2, 100));
        fwrite($this->tempFile, $fakeIcoFileData);
        $sut = new IcoFileMock(self::$testFileName);
        $sut->render();
        $this->expectOutputString($fakeIcoFileData);
    }
}
