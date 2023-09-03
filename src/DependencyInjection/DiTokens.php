<?php

declare(strict_types=1);

namespace Phpolar\Phpolar\DependencyInjection;

/**
 * Contains dependency injection tokens.
 */
final class DiTokens
{
    public const CSRF_CHECK_MIDDLEWARE = "CSRF_CHECK_MIDDLEWARE";
    public const CSRF_RESPONSE_FILTER_MIDDLEWARE = "CSRF_RESPONSE_FILTER_MIDDLEWARE";
    public const ERROR_HANDLER_401 = "ERROR_HANDLER_401";
    public const ERROR_HANDLER_404 = "ERROR_HANDLER_404";
    public const RESPONSE_EMITTER = "RESPONSE_EMITTER";
    public const UNAUTHORIZED_HANDLER = "UNAUTHORIZED_HANDLER";
}
