<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Comparables;

use Phpolar\Phpolar\Core\Comparable;

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
