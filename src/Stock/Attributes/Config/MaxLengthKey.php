<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes\Config;

use Phpolar\Phpolar\Api\Attributes\Config\Key;
use Phpolar\Phpolar\Stock\Attributes\MaxLength;

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
