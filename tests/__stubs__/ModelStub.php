<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Stubs;

use Phpolar\Phpolar\Model\AbstractModel;

final class ModelStub extends AbstractModel
{
    public string $prop1;
    public int $prop2;
    public bool $prop3;
}
