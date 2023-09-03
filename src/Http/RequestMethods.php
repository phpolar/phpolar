<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\Http;

/**
 * Contains the currently supported
 * HTTP request methods supported for
 * routing.
 */
enum RequestMethods
{
    case GET;
    case POST;
}
