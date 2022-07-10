<?php

namespace Efortmeyer\Polar\Tests\Mocks;

use Serializable;

final class SerializableMock implements Serializable
{
    public function serialize()
    {
        return "<a href='javascript:alert(document.cookie)'>hacked</a>";
    }

    public function unserialize($data)
    {
    }
}
