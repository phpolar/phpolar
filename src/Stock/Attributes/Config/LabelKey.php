<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Stock\Attributes\Config;

use Phpolar\Phpolar\Api\Attributes\Config\Key;
use Phpolar\Phpolar\Stock\Attributes\Label;

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
