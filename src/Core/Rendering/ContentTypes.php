<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Core\Rendering;

/**
 * Contains a set of content types for files and streams.
 */
enum ContentTypes: string
{
    case HTML = "text/html";

    case ICO = "image/x-icon";
}
