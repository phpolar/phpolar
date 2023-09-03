<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Mocks;

use Phpolar\Phpolar\Api\Model;

final class NonMatchingPropModel extends Model
{
    /**
     * @var string
     */
    public $nonMatchingProperty = "I JUST DON'T MATCH";
}
