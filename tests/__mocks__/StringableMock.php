<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\Mocks;

use Stringable;

class StringableMock implements Stringable
{
    public function __toString()
    {
        return "<a href='javascript:alert(document.cookie)'>hacked</a>";
    }
}
