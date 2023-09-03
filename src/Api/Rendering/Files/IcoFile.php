<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\Rendering\Files;

use Efortmeyer\Polar\Api\Rendering\Files\File;
use Efortmeyer\Polar\Core\Rendering\ContentTypes;

class IcoFile extends File
{
    protected function getContentType(): string
    {
        return ContentTypes::ICO->value;
    }
}
