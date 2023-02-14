<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api\Rendering\Files;

use Phpolar\Phpolar\Tests\Mocks\IcoFileMock;
use Phpolar\Phpolar\Tests\Extensions\PhpolarTestCaseExtension;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Phpolar\Phpolar\Api\Rendering\Files\File
 *
 * @uses \Phpolar\Phpolar\Api\Rendering\Files\IcoFile
 */
class FileTest extends PhpolarTestCaseExtension
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
