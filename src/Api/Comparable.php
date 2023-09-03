<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api;

/**
 * Provides support for equality comparison.
 */
interface Comparable
{
    public function equals(Comparable $other): bool;
}
