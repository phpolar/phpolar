<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Core;

/**
 * Provides support for equality comparison.
 */
interface Comparable
{
    public function equals(Comparable $other): bool;
}
