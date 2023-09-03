<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\Comparables;

use Efortmeyer\Polar\Api\Comparable;

class NestedXSSHackEnd implements Comparable
{
    /**
     * @var string
     */
    public $hack = "<a href='javascript:alert(document.cookie)'>hacked</a>";

    public function equals(Comparable $other): bool
    {
        return $this->hack === $other->hack;
    }
}
