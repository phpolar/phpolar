<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes\Config;

use Phpolar\Phpolar\Api\Attributes\Config\Key;
use Phpolar\Phpolar\Stock\Attributes\Column;

final class ColumnKey extends Key
{
    public function getKey(): string
    {
        return Column::class;
    }

    public function __toString(): string
    {
        return Column::class;
    }
}
