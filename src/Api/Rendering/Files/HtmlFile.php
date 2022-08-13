<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\Rendering\Files;

use Efortmeyer\Polar\Api\Rendering\Files\File;

class HtmlFile extends File
{
    protected function setContentTypeHeader(): void
    {
        header("Content-Type: text/html");
    }
}
