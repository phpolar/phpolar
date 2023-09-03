<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Fakes;

use Phpolar\Routable\RoutableInterface;

final class FakeRoutable implements RoutableInterface
{
    public function process(): string
    {
        return "";
    }
}
