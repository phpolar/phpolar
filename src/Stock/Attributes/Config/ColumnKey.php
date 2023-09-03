<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes\Config;

use Efortmeyer\Polar\Api\Attributes\Config\Key;
use Efortmeyer\Polar\Stock\Attributes\Column;

final class ColumnKey extends Key
{
    public function getKey(): string
    {
        return Column::class;
    }

    public function __toString()
    {
        return Column::class;
    }
}
