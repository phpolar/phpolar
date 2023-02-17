<?php

/**
 * This file contains all globs (pathname patterns)
 * used by the framework.
 *
 * Having them here makes them easier to find when
 * debugging or making changes.
 */

declare(strict_types=1);

namespace Phpolar\Phpolar\Config;

/**
 * Contains all pathname patterns
 * used by the framework.
 */
enum Globs : string
{
    /**
     * The frameworks dependencies configuration
     * is located in the source files of the framework.
     */
    case FrameworkDeps = __DIR__ . "/dependencies/framework.php";
    /**
     * The custom dependencies directory should be
     * set up in the application using the framework.
     */
    case CustomDeps = "{src/,}config/dependencies/conf.d/*.php";
}
