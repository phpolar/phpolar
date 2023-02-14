<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Core\Rendering;

use Phpolar\Phpolar\Api\Rendering\TemplateContext;

/**
 * Provides a way to bind a template context's variables
 * to a template file.
 */
final class Template
{
    /**
     * Displays the content of a template file.
     *
     * The template context's variables will be used in the
     * template file.
     *
     * @api
     */
    public function render(TemplateContext $context, string $templatePath): void
    {
        $closure = function (string $pathToFile): void {
            ob_start();
            include $pathToFile;
            ob_end_flush();
        };
        $context = (new HtmlEncoder())->encodeProperties($context);
        $closure = $closure->bindTo($context, $context);
        $closure($templatePath);
    }
}
