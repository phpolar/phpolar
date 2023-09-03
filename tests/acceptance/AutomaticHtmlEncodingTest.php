<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use Phpolar\PhpTemplating\Binder;
use Phpolar\PhpTemplating\Dispatcher;
use Phpolar\PhpTemplating\FileRenderingStrategy;
use Phpolar\PhpTemplating\HtmlSafeContext;
use Phpolar\PhpTemplating\TemplateEngine;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @coversNothing
 */
final class AutomaticHtmlEncodingTest extends TestCase
{
    protected function getTemplateEngine()
    {

        return new TemplateEngine(
            new FileRenderingStrategy(),
            new Binder(),
            new Dispatcher(),
        );
    }

    /**
     * @test
     * @testdox Should prevent cross-site scripting injection
     */
    public function criterion1()
    {
        $templatingEngine = $this->getTemplateEngine();
        $objWithHacks = new class() {
            public string $hack1 = "<script>alert('hacked');</script>";
            public string $directiveHack1 = "javascript:alert('hacked');";
            public string $directiveHack2 = "# javascript:alert('hacked');";
            public string $directiveHack3 = "/ javascript:alert('hacked');";
        };
        $mitigated = <<<HTML
        &lt;script&gt;alert&lpar;&apos;hacked&apos;&rpar;&semi;&lt;&sol;script&gt;
        <a alert&lpar;&apos;hacked&apos;&rpar;&semi;>HACK</a>
        <a href=&num; alert&lpar;&apos;hacked&apos;&rpar;&semi;>HACK</a>
        <img src=&sol; alert&lpar;&apos;hacked&apos;&rpar;&semi; />
        HTML;
        $this->expectOutputString($mitigated);
        $templatingEngine->render("tests/__templates__/hack.php", new HtmlSafeContext($objWithHacks));
    }
}
