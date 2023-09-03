<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes\Config;

use Efortmeyer\Polar\Api\Attributes\Config\Key;
use Efortmeyer\Polar\Stock\Attributes\MaxLength;

final class MaxLengthKey extends Key
{
    public function getKey(): string
    {
        return MaxLength::class;
    }

    public function __toString(): string
    {
        return MaxLength::class;
    }
}
