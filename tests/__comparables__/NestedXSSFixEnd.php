<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Comparables;

use Phpolar\Phpolar\Core\Comparable;

class NestedXSSFixEnd implements Comparable
{
    /**
     * @var string
     */
    public $hack = "&lt;a href&equals;&apos;javascript&colon;alert&lpar;document&period;cookie&rpar;&apos;&gt;hacked&lt;&sol;a&gt;";

    public function equals(Comparable $other): bool
    {
        return $this->hack === $other->hack;
    }
}
