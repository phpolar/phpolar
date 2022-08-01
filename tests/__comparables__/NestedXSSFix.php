<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\Comparables;

use Efortmeyer\Polar\Core\Comparable;

class NestedXSSFix implements Comparable
{
    /**
     * @var string
     */
    public $hack = "&lt;a href&equals;&apos;javascript&colon;alert&lpar;document&period;cookie&rpar;&apos;&gt;hacked&lt;&sol;a&gt;";

    /**
     * @var NestedXSSFix
     */
    public $child;

    public function equals(Comparable $other): bool
    {
        return $this->hack === $other->hack && $this->child->equals($other->child);
    }
}
