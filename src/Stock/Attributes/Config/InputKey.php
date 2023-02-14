<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes\Config;

use Phpolar\Phpolar\Api\Attributes\Config\Key;
use Phpolar\Phpolar\Stock\Attributes\Input;

final class InputKey extends Key
{
    public function getKey(): string
    {
        return Input::class;
    }

    public function __toString(): string
    {
        return Input::class;
    }
}
