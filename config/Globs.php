<?php

/**
 * This file contains all globs (pathname patterns)
 * used by the framework.
 */

declare(strict_types=1);

namespace Phpolar\Phpolar\Config;

/**
 * Contains all pathname patterns
 * used by the framework.
 */
enum Globs: string
{
    /**
     * The frameworks dependencies configuration
     * is located in the source files of the framework.
     */
    case FrameworkDeps = "{vendor/phpolar/phpolar/,}config/dependencies/framework.php";
    /**
     * The custom dependencies directory should be
     * set up in the application using the framework.
     */
    case CustomDeps = "{src/,}config/dependencies/conf.d/*.php";
}
