<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\UIElements;

use Efortmeyer\Polar\Core\Defaults;

class SuccessBanner extends Banner
{
    protected const BACKGROUND_COLOR = "#44ff33";

    public function getMessage(): string
    {
        return Defaults::SUCCESS_MESSAGE;
    }
}
