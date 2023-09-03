<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Tests\Mocks;

use Efortmeyer\Polar\Api\Model;

final class ModelSubclass extends Model
{
    /**
     * @var string
     */
    public $property1 = "FAKE VALUE";

    /**
     * @var string
     */
    public $property2 = "ANOTHER FAKE VALUE";

    /**
     * @var string
     */
    public $property3 = "AGAIN... ANOTHER FAKE VALUE";
}
