<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

use RuntimeException;

final class InvalidHtmlResponseException extends RuntimeException
{
    public function __construct(
        string $message = "An HTML response was expected but another type was returned from the Request Processor. If you want the value you returned to be serialized by the framework, please configure acceptable representations for the target resource."
    ) {
        parent::__construct($message);
    }
}
