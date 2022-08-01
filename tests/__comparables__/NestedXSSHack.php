<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\Comparables;

use Efortmeyer\Polar\Core\Comparable;

class NestedXSSHack implements Comparable
{
    /**
     * @var string
     */
    public $hack = "<a href='javascript:alert(document.cookie)'>hacked</a>";

    /**
     * @var NestedXSSHack
     */
    public $child;

    public function equals(Comparable $other): bool
    {
        return $this->hack === $other->hack && $this->child->equals($other->child);
    }
}
