<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Api\UIElements;

class SuccessBanner extends Banner
{
    protected const BACKGROUND_COLOR = "#44ff33";

    public function getMessage(): string
    {
        return Messages::SUCCESS_MESSAGE;
    }
}
