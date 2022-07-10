<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes\Config;

use Efortmeyer\Polar\Api\Attributes\Config\Key;
use Efortmeyer\Polar\Stock\Attributes\Label;

final class LabelKey extends Key
{
    public function getKey(): string
    {
        return Label::class;
    }

    public function __toString(): string
    {
        return Label::class;
    }
}
