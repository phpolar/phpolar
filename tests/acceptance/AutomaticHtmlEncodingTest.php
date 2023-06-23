<?php

declare(strict_types=1);

namespace Phpolar\Phpolar;

use Phpolar\PurePhp\Binder;
use Phpolar\PurePhp\Dispatcher;
use Phpolar\PurePhp\FileRenderingStrategy;
use Phpolar\PurePhp\HtmlSafeContext;
use Phpolar\PurePhp\TemplateEngine;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[TestDox("Automatic HTML Encoding")]
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

    #[Test]
    #[TestDox("Shall prevent cross-site scripting injection")]
    public function criterion1()
    {
        $templatingEngine = $this->getTemplateEngine();
        $objWithHacks = new class () {
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
