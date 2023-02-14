<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api\Rendering\Files;

use Phpolar\Phpolar\Api\Rendering\Files\File;
use Phpolar\Phpolar\Core\Rendering\ContentTypes;

class HtmlFile extends File
{
    protected function getContentType(): string
    {
        return ContentTypes::HTML->value;
    }
}
