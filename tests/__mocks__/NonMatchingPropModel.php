<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\Mocks;

use Efortmeyer\Polar\Api\Model;

final class NonMatchingPropModel extends Model
{
    /**
     * @var string
     */
    public $nonMatchingProperty = "I JUST DON'T MATCH";
}
