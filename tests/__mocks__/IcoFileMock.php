<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Mocks;

use Phpolar\Phpolar\Api\Rendering\Files\IcoFile;

class IcoFileMock extends IcoFile
{
    protected function printContentType(): void
    {
    }
}
