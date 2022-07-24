<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Rendering;

use Efortmeyer\Polar\Api\Rendering\TemplateContext;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Efortmeyer\Polar\Core\Rendering\Template
 *
 * @uses \Efortmeyer\Polar\Core\Rendering\HtmlEncoder
 * @uses \Efortmeyer\Polar\Api\Rendering\TemplateContext
 */
class TemplateTest extends TestCase
{
    /**
     * @var resource
     */
    protected $inMemoryFile;

    protected function setUp(): void
    {
        $this->inMemoryFile = fopen(FAKE_HTML_FILE_PATH, "c+");
    }

    protected function tearDown(): void
    {
        fclose($this->inMemoryFile);
        unlink(FAKE_HTML_FILE_PATH);
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
        $sut->render($templateContext, FAKE_HTML_FILE_PATH);
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
        $sut->render($templateContext, FAKE_HTML_FILE_PATH);
        $this->expectOutputRegex("/<h1>{$sanitized}<\/h1>/");
        $this->expectOutputRegex("/(?!.*<h1>{$vulnerableStringPattern}<\/h1>(.*?))/s");
    }
}
