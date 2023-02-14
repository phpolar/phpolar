<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Comparables;

use Phpolar\Phpolar\Core\Comparable;

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
