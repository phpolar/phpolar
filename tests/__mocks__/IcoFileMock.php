<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\Mocks;

use Efortmeyer\Polar\Api\Rendering\Files\IcoFile;

class IcoFileMock extends IcoFile
{
    protected function printContentType(): void
    {
    }
}
