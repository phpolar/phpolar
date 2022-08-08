<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core\Rendering;

/**
 * Contains a set of content types for files and streams.
 */
enum ContentTypes: string
{
    case HTML = "text/html";

    case ICO = "image/x-icon";
}
