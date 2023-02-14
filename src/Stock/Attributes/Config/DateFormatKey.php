<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes\Config;

use Phpolar\Phpolar\Api\Attributes\Config\Key;
use Phpolar\Phpolar\Stock\Attributes\DateFormat;

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
