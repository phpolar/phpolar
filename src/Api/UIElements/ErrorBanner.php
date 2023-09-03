<?php

declare(strict_types=1);

namespace Efortmeyer\Polar\Api\UIElements;

use Efortmeyer\Polar\Core\Defaults;

class ErrorBanner extends Banner
{
    protected const BACKGROUND_COLOR = "#ffd0d0";

    protected const MESSAGE = Defaults::ERROR_MESSAGE;

    public function getMessage(): string
    {
        return self::MESSAGE;
    }
}
