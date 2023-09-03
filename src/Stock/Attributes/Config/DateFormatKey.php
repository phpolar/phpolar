<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Stock\Attributes\Config;

use Efortmeyer\Polar\Api\Attributes\Config\Key;
use Efortmeyer\Polar\Stock\Attributes\DateFormat;

final class DateFormatKey extends Key
{
    public function getKey(): string
    {
        return DateFormat::class;
    }

    public function __toString(): string
    {
        return DateFormat::class;
    }
}
