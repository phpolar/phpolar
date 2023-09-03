<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\Rendering\Files;

use Efortmeyer\Polar\Tests\Mocks\IcoFileMock;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Api\Rendering\Files\File
 *
 * @uses \Efortmeyer\Polar\Api\Rendering\Files\IcoFile
 */
class FileTest extends TestCase
{
    /**
     * @var resource
     */
    protected $tempFile;

    protected function setUp(): void
    {
        $this->tempFile = fopen(FAKE_ICO_FILE_PATH, "c+");
    }

    protected function tearDown(): void
    {
        fclose($this->tempFile);
        unlink(FAKE_ICO_FILE_PATH);
    }

    /**
     * @test
     */
    public function shouldRenderFileContents()
    {
        $fakeIcoFileData = str_repeat("FAKE ", random_int(2, 100));
        fwrite($this->tempFile, $fakeIcoFileData);
        $sut = new IcoFileMock(FAKE_ICO_FILE_PATH);
        $sut->render();
        $this->expectOutputString($fakeIcoFileData);
    }
}
