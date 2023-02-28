<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Config;

/**
 * Contains formattable strings.
 */
enum Formats : string
{
    /**
     * The default location of custom error templates.
     */
    case ErrorTemplates = "src/templates/%s.phtml";
    /**
     * The default error text to display
     */
    case ErrorText = "<h1>%s</h1>";
}
