<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes\Config;

use Efortmeyer\Polar\Api\Attributes\Config\Key;
use Efortmeyer\Polar\Stock\Attributes\Input;

final class InputKey extends Key
{
    public function getKey(): string
    {
        return Input::class;
    }

    public function __toString()
    {
        return Input::class;
    }
}
