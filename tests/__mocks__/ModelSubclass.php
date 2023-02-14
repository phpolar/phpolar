<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Tests\Mocks;

use Phpolar\Phpolar\Api\Model;

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
