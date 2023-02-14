<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Core;

/**
 * Provides support for equality comparison.
 */
interface Comparable
{
    public function equals(Comparable $other): bool;
}
