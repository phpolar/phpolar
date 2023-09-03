<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api\UIElements;

class ErrorBanner extends Banner
{
    protected const BACKGROUND_COLOR = "#ffd0d0";

    protected const MESSAGE = Messages::ERROR_MESSAGE;

    public function getMessage(): string
    {
        return self::MESSAGE;
    }
}
