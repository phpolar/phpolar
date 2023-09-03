<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Rendering;

use Efortmeyer\Polar\Api\Rendering\TemplateContext;
use Efortmeyer\Polar\Tests\Extensions\PolarTestCaseExtension;

/**
 * @covers \Efortmeyer\Polar\Core\Rendering\Template
 *
 * @uses \Efortmeyer\Polar\Core\Rendering\HtmlEncoder
 * @uses \Efortmeyer\Polar\Api\Rendering\TemplateContext
 */
class TemplateTest extends PolarTestCaseExtension
{
    /**
     * @var resource
     */
    protected $inMemoryFile;

    protected static $testFileName;

    protected function setUp(): void
    {
        self::$testFileName = self::getTestFileName(".html");
        $this->inMemoryFile = fopen(self::$testFileName, "c+");
    }

    protected function tearDown(): void
    {
        fclose($this->inMemoryFile);
        unlink(self::$testFileName);
    }

    /**
     * @test
     */
    public function shouldRenderTempateWithBoundContextVariables()
    {
        $fakeHtmlContent = <<<'HTML'
        <h1><?= $this->title ?></h1>
        HTML;
        $templateContext = new class() extends TemplateContext
        {
            /**
             * @var string
             */
            public $title = "FAKE TITLE";
        };
        fwrite($this->inMemoryFile, $fakeHtmlContent);
        $sut = new Template();
        $sut->render($templateContext, self::$testFileName);
        $this->expectOutputRegex("/<h1>FAKE TITLE<\/h1>/");
    }

    /**
     * @test
     */
    public function shouldSanitizeValuesOfBoundContextVariables()
    {
        $vulnerableString = "<a href='javascript:alert(document.cookie)'>hacked</a>";
        $vulnerableStringPattern = "<a href='javascript:alert\(document.cookie\)'>hacked<\/a>";
        $sanitized = "&lt;a href&equals;&apos;javascript&colon;alert&lpar;document&period;cookie&rpar;&apos;&gt;hacked&lt;&sol;a&gt;";;
        $fakeHtmlContent = <<<'HTML'
        <h1><?= $this->title ?></h1>
        HTML;
        $templateContext = new class($vulnerableString) extends TemplateContext
        {
            /**
             * @var string
             */
            public $title;

            public function __construct($title)
            {
                $this->title = $title;
            }
        };
        fwrite($this->inMemoryFile, $fakeHtmlContent);
        $sut = new Template();
        $sut->render($templateContext, self::$testFileName);
        $this->expectOutputRegex("/<h1>{$sanitized}<\/h1>/");
        $this->expectOutputRegex("/(?!.*<h1>{$vulnerableStringPattern}<\/h1>(.*?))/s");
    }
}
